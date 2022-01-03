<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web;

use DrdPlus\RulesSkeleton\Cache\CacheInterface;
use DrdPlus\RulesSkeleton\Redirect;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringInterface;
use Granam\WebContentBuilder\HtmlDocument;
use Granam\WebContentBuilder\Web\HtmlContentInterface;

class RulesContent extends StrictObject implements StringInterface
{
    public const TABLES = 'tables';
    public const FULL = ' full';
    public const PDF = 'pdf';
    public const GATEWAY = 'gateway';
    public const NOT_FOUND = 'not_found';

    private \Granam\WebContentBuilder\Web\HtmlContentInterface $htmlContent;
    private \DrdPlus\RulesSkeleton\Web\Head $head;
    private \DrdPlus\RulesSkeleton\Cache\CacheInterface $cache;
    private \DrdPlus\RulesSkeleton\Web\RulesHtmlDocumentPostProcessor $rulesHtmlDocumentPostProcessor;
    private string $contentType;
    private ?\DrdPlus\RulesSkeleton\Redirect $redirect = null;
    private ?\Granam\WebContentBuilder\HtmlDocument $htmlDocument = null;

    public function __construct(
        HtmlContentInterface $htmlContent,
        CacheInterface $cache,
        RulesHtmlDocumentPostProcessor $rulesHtmlDocumentPostProcessor,
        string $contentType,
        ?Redirect $redirect
    )
    {
        $this->htmlContent = $htmlContent;
        $this->cache = $cache;
        $this->rulesHtmlDocumentPostProcessor = $rulesHtmlDocumentPostProcessor;
        $this->contentType = $contentType;
        $this->redirect = $redirect;
    }

    public function containsTables(): bool
    {
        return $this->contentType === self::TABLES;
    }

    public function containsFull(): bool
    {
        return $this->contentType === self::FULL;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        if ($this->containsPdf()) {
            return $this->htmlContent->getValue();
        }
        $cachedContent = $this->getCachedContent();
        if ($cachedContent !== null) {
            // redirection is not cached
            return $this->injectRedirectIfAny($cachedContent);
        }
        $previousMemoryLimit = \ini_set('memory_limit', '1G');
        $htmlDocument = $this->buildHtmlDocument();
        $content = $htmlDocument->saveHTML();
        $this->cache->cacheContent($content);
        if ($previousMemoryLimit !== false) {
            \ini_set('memory_limit', $previousMemoryLimit);
        }

        // has to be AFTER cache as we do not want to cache it
        return $this->injectRedirectIfAny($content);
    }

    protected function buildHtmlDocument(): HtmlDocument
    {
        if ($this->htmlDocument === null) {
            $htmlDocument = $this->htmlContent->getHtmlDocument();
            $this->rulesHtmlDocumentPostProcessor->processDocument($htmlDocument);
            $this->htmlDocument = $htmlDocument;
        }

        return $this->htmlDocument;
    }

    private function getCachedContent(): ?string
    {
        if ($this->cache->isCacheValid()) {
            return $this->cache->getCachedContent();
        }

        return null;
    }

    private function injectRedirectIfAny(string $content): string
    {
        if (!$this->getRedirect()) {
            return $content;
        }
        $cachedDocument = new HtmlDocument($content);
        $meta = $cachedDocument->createElement('meta');
        $meta->setAttribute('http-equiv', 'Refresh');
        $meta->setAttribute('content', $this->getRedirect()->getAfterSeconds() . '; url=' . $this->getRedirect()->getTarget());
        $meta->setAttribute('id', 'meta_redirect');
        $cachedDocument->head->appendChild($meta);

        return $cachedDocument->saveHTML();
    }

    private function getRedirect(): ?Redirect
    {
        return $this->redirect;
    }

    public function containsPdf(): bool
    {
        return $this->contentType === self::PDF;
    }

    public function containsPass(): bool
    {
        return $this->contentType === self::GATEWAY;
    }

    public function containsNotFound(): bool
    {
        return $this->contentType === self::NOT_FOUND;
    }

}
