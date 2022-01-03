<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton;

use DrdPlus\CalculatorSkeleton\Web\CalculatorWebPartsContainer;
use DrdPlus\RulesSkeleton\Cache\Cache;
use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\Web\Main\MainContent;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\ServicesContainer;
use DrdPlus\RulesSkeleton\Web\Tools\WebPartsContainer;
use Granam\Git\Git;
use Granam\String\StringTools;

/**
 * @method CalculatorConfiguration getConfiguration()
 */
class CalculatorServicesContainer extends ServicesContainer
{
    private ?CalculatorRequest $calculatorRequest = null;
    private ?Memory $memory = null;
    private ?DateTimeProvider $dateTimeProvider = null;
    private ?CurrentValues $currentValues = null;
    private ?History $history = null;
    private ?MainContent $calculatorWebContent = null;
    private ?CalculatorWebPartsContainer $calculatorRoutedWebPartsContainer = null;
    private ?CalculatorWebPartsContainer $calculatorRootWebPartsContainer = null;
    private ?GitUpdater $gitUpdater = null;

    public function __construct(
        CalculatorConfiguration $calculatorConfiguration,
        Environment $environment,
        HtmlHelper $htmlHelper
    )
    {
        parent::__construct($calculatorConfiguration, $environment, $htmlHelper);
    }

    /**
     * @return Request|CalculatorRequest
     */
    public function getRequest(): Request
    {
        if ($this->calculatorRequest === null) {
            $this->calculatorRequest = CalculatorRequest::createFromGlobals($this->getBotParser(), $this->getEnvironment());
        }
        return $this->calculatorRequest;
    }

    public function getMemory(): Memory
    {
        if ($this->memory === null) {
            $this->memory = new Memory(
                $this->getMemoryStorage(),
                $this->getDateTimeProvider(),
                $this->getConfiguration()->getCookiesTtl()
            );
        }
        return $this->memory;
    }

    protected function getMemoryStorage(): CookiesStorage
    {
        return new CookiesStorage($this->getCookiesService(), $this->getCookiesStorageKeyPrefix() . '-memory');
    }

    public function getDateTimeProvider(): DateTimeProvider
    {
        if ($this->dateTimeProvider === null) {
            $this->dateTimeProvider = new DateTimeProvider(new \DateTimeImmutable());
        }
        return $this->dateTimeProvider;
    }

    protected function getCookiesStorageKeyPrefix(): string
    {
        return StringTools::getClassBaseName(static::class) . '-' . $this->getConfiguration()->getCookiesPostfix();
    }

    public function getCurrentValues(): CurrentValues
    {
        if ($this->currentValues === null) {
            $this->currentValues = new CurrentValues($this->getRequest()->getValuesFromGet(), $this->getMemory());
        }
        return $this->currentValues;
    }

    public function getHistory(): History
    {
        if ($this->history === null) {
            $this->history = new History(
                $this->getHistoryStorage(),
                $this->getDateTimeProvider(),
                $this->getConfiguration()->getCookiesTtl()
            );
        }
        return $this->history;
    }

    protected function getHistoryStorage(): CookiesStorage
    {
        return new CookiesStorage($this->getCookiesService(), $this->getCookiesStorageKeyPrefix() . '-history');
    }

    public function getRulesMainContent(): MainContent
    {
        if ($this->calculatorWebContent === null) {
            $this->calculatorWebContent = new MainContent(
                $this->getHtmlHelper(),
                $this->getEnvironment(),
                $this->getHead(),
                $this->getRoutedWebPartsContainer()->getRulesMainBody()
            );
        }
        return $this->calculatorWebContent;
    }

    public function getRoutedWebPartsContainer(): WebPartsContainer
    {
        if ($this->calculatorRoutedWebPartsContainer === null) {
            $this->calculatorRoutedWebPartsContainer = new CalculatorWebPartsContainer(
                $this->getConfiguration(),
                $this->getUsagePolicy(),
                $this->getRoutedWebFiles(),
                $this->getDirs(),
                $this->getHtmlHelper(),
                $this->getRequest()
            );
        }
        return $this->calculatorRoutedWebPartsContainer;
    }

    public function getRootWebPartsContainer(): WebPartsContainer
    {
        if ($this->calculatorRootWebPartsContainer === null) {
            $this->calculatorRootWebPartsContainer = new CalculatorWebPartsContainer(
                $this->getConfiguration(),
                $this->getUsagePolicy(),
                $this->getRootWebFiles(),
                $this->getDirs(),
                $this->getHtmlHelper(),
                $this->getRequest()
            );
        }
        return $this->calculatorRootWebPartsContainer;
    }

    public function getGit(): Git
    {
        if ($this->gitUpdater === null) {
            $this->gitUpdater = new GitUpdater();
        }
        return $this->gitUpdater;
    }

    public function getTablesWebCache(): Cache
    {
        return $this->getDummyWebCache();
    }

    public function getGatewayWebCache(): Cache
    {
        return $this->getDummyWebCache();
    }

    public function getPassedWebCache(): Cache
    {
        return $this->getDummyWebCache();
    }

}
