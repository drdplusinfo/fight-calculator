<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\Cache\CacheIdProvider;
use DrdPlus\RulesSkeleton\Cache\CacheInterface;
use DrdPlus\RulesSkeleton\Web\Menu\MenuBodyInterface;
use DrdPlus\RulesSkeleton\Web\RulesContent;
use DrdPlus\RulesSkeleton\Web\RulesHtmlDocumentPostProcessor;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\Exceptions\UnknownWebFilesDir;
use Granam\WebContentBuilder\Web\HtmlContentInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RulesApplication extends StrictObject
{
    private ServicesContainer $servicesContainer;
    private ?RulesContent $content = null;
    private ?Redirect $redirect = null;
    private ?bool $canPassIn = null;
    private ?RulesContent $notFoundContent = null;

    public function __construct(ServicesContainer $servicesContainer)
    {
        $this->servicesContainer = $servicesContainer;
    }

    public function run(): void
    {
        try {
            $this->sendCustomHeaders();
            if ($this->isRequestedWebVersionUpdate()) {
                echo $this->updateCode();
                $this->clearCache();
            } else {
                $this->persistCurrentVersion();
                echo $this->getContent()->getValue();
            }
        } catch (UnknownWebFilesDir | RouteNotFoundException | ResourceNotFoundException $invalidRoute) {
            $this->sendNotFoundHeaders();
            echo $this->getNotFoundRulesContent()->getValue();
        }
    }

    private function isRequestedWebVersionUpdate(): bool
    {
        return $this->servicesContainer->getRequest()->getValue(Request::UPDATE) === 'web';
    }

    protected function updateCode(): string
    {
        return \implode(
            "\n",
            $this->servicesContainer->getGit()->update($this->servicesContainer->getDirs()->getProjectRoot())
        );
    }

    protected function clearCache()
    {
        $this->servicesContainer->getCacheCleaner()->clearCache();
    }

    private function persistCurrentVersion(): bool
    {
        return $this->servicesContainer->getCookiesService()->setMinorVersionCookie(
            $this->servicesContainer->getCurrentWebVersion()->getCurrentMinorVersion()
        );
    }

    protected function getContent(): RulesContent
    {
        if (!$this->content) {
            $this->content = $this->createContent();
        }

        return $this->content;
    }

    protected function createContent(): RulesContent
    {
        if ($this->servicesContainer->getTablesRequestDetector()->areTablesRequested()) {
            return $this->createRulesContentWithTables();
        }
        if ($this->servicesContainer->getRequest()->isRequestedPdf()
            && $this->servicesContainer->getRoutedWebPartsContainer()->getPdfBody()->getPdfFile()
        ) {
            return $this->createRulesContentWithPdf();
        }
        if (!$this->canPassIn()) {
            return $this->createRulesContentWithGateway();
        }
        return $this->createRulesContentWithFullContent();
    }

    protected function createRulesContentWithTables(): RulesContent
    {
        $rulesHtmlDocumentPostProcessor = $this->createRulesHtmlDocumentPostProcessor(
            $this->servicesContainer->getPassedMenuBody(),
            $this->servicesContainer->getTablesWebCache()
        );
        return $this->createRulesContent(
            $this->servicesContainer->getTablesContent(),
            $this->servicesContainer->getTablesWebCache(),
            RulesContent::TABLES,
            $rulesHtmlDocumentPostProcessor
        );
    }

    protected function createRulesHtmlDocumentPostProcessor(
        MenuBodyInterface $menuBody,
        CacheIdProvider $cacheIdProvider
    ): RulesHtmlDocumentPostProcessor
    {
        return new RulesHtmlDocumentPostProcessor($menuBody, $this->servicesContainer->getCurrentWebVersion(), $cacheIdProvider);
    }

    protected function createRulesContentWithPdf(): RulesContent
    {
        $rulesHtmlDocumentPostProcessor = $this->createRulesHtmlDocumentPostProcessor(
            $this->servicesContainer->getEmptyMenuBody(),
            $this->servicesContainer->getDummyWebCache()
        );
        return $this->createRulesContent(
            $this->servicesContainer->getPdfContent(),
            $this->servicesContainer->getDummyWebCache(),
            RulesContent::PDF,
            $rulesHtmlDocumentPostProcessor
        );
    }

    protected function createRulesContentWithGateway(): RulesContent
    {
        $rulesHtmlDocumentPostProcessor = $this->createRulesHtmlDocumentPostProcessor(
            $this->servicesContainer->getGatewayMenuBody(),
            $this->servicesContainer->getGatewayWebCache()
        );
        return $this->createRulesContent(
            $this->servicesContainer->getGatewayContent(),
            $this->servicesContainer->getGatewayWebCache(),
            RulesContent::GATEWAY,
            $rulesHtmlDocumentPostProcessor
        );
    }

    protected function createRulesContentWithFullContent(): RulesContent
    {
        $rulesHtmlDocumentPostProcessor = $this->createRulesHtmlDocumentPostProcessor(
            $this->servicesContainer->getPassedMenuBody(),
            $this->servicesContainer->getPassedWebCache()
        );
        return $this->createRulesContent(
            $this->servicesContainer->getRulesMainContent(),
            $this->servicesContainer->getPassedWebCache(),
            RulesContent::FULL,
            $rulesHtmlDocumentPostProcessor
        );
    }

    protected function createRulesContent(HtmlContentInterface $content, CacheInterface $cache, string $contentType, RulesHtmlDocumentPostProcessor $rulesHtmlDocumentPostProcessor): RulesContent
    {
        return new RulesContent(
            $content,
            $cache,
            $rulesHtmlDocumentPostProcessor,
            $contentType,
            $this->getRedirect()
        );
    }

    private function getRedirect(): ?Redirect
    {
        return $this->redirect;
    }

    private function canPassIn(): bool
    {
        if ($this->canPassIn !== null) {
            return $this->canPassIn;
        }
        if (!$this->servicesContainer->getTicket()->canPassIn()) {
            if ($this->servicesContainer->getRequest()->getValueFromPost(Request::CONFIRM)) {
                $this->servicesContainer->getUsagePolicy()->confirmOwnershipOfVisitor(new \DateTime('+1 year'));
                $this->checkThatCanPassNow();
            } elseif ($this->servicesContainer->getRequest()->getValue(Request::TRIAL)) {
                $this->activateTrial($this->servicesContainer->getNow());
                $this->checkThatCanPassNow();
            }
        }

        return $this->canPassIn = $this->servicesContainer->getTicket()->canPassIn();
    }

    private function checkThatCanPassNow(): void
    {
        if (!$this->servicesContainer->getTicket()->canPassIn()) {
            throw new Exceptions\CanNotPassIn('Visitor should be able to pass in but still can not');
        }
    }

    private function activateTrial(\DateTimeImmutable $now): bool
    {
        $trialExpiration = $now->modify('+4 minutes');
        $visitorCanAccessContent = $this->servicesContainer->getUsagePolicy()->activateTrial($trialExpiration);
        if ($visitorCanAccessContent) {
            $at = $trialExpiration->getTimestamp() + 1; // one second "insurance" overlap
            $afterSeconds = $at - $now->getTimestamp();
            $this->setRedirect(new Redirect(\sprintf('/?%s=%d', Request::TRIAL_EXPIRED_AT, $at), $afterSeconds));
        }

        return $visitorCanAccessContent;
    }

    private function setRedirect(Redirect $redirect): void
    {
        $this->redirect = $redirect;
        $this->content = null; // unset Content to re-create it with new redirect
    }

    private function sendCustomHeaders(): void
    {
        if ($this->getContent()->containsTables()) {
            if ($this->servicesContainer->getRequest()->isCliRequest()) {
                return;
            }
            // anyone can show content of this page
            \header('Access-Control-Allow-Origin: *');
        } elseif ($this->getContent()->containsPdf()) {
            $pdfFile = $this->servicesContainer->getRoutedWebPartsContainer()->getPdfBody()->getPdfFile();
            $pdfFileBasename = \basename($pdfFile);
            if ($this->servicesContainer->getRequest()->isCliRequest()) {
                return;
            }
            \header('Content-type: application/pdf');
            \header('Content-Length: ' . \filesize($pdfFile));
            \header("Content-Disposition: attachment; filename=\"$pdfFileBasename\"");
        }
    }

    private function sendNotFoundHeaders(): void
    {
        if ($this->servicesContainer->getRequest()->isCliRequest()) {
            return;
        }
        http_response_code(404);
    }

    protected function getNotFoundRulesContent(): RulesContent
    {
        if (!$this->notFoundContent) {
            $this->notFoundContent = $this->createRulesContentForNotFound();
        }

        return $this->notFoundContent;
    }

    protected function createRulesContentForNotFound(): RulesContent
    {
        $rulesHtmlDocumentPostProcessor = $this->createRulesHtmlDocumentPostProcessor(
            !$this->canPassIn()
                ? $this->servicesContainer->getGatewayMenuBody()
                : $this->servicesContainer->getPassedMenuBody(),
            $this->servicesContainer->getNotFoundCache()
        );
        return $this->createRulesContent(
            $this->servicesContainer->getNotFoundContent(),
            $this->servicesContainer->getNotFoundCache(),
            RulesContent::NOT_FOUND,
            $rulesHtmlDocumentPostProcessor
        );
    }
}
