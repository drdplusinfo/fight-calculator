<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web;

use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\HtmlHelper;
use Granam\WebContentBuilder\HtmlDocument;
use Granam\WebContentBuilder\Web\HeadInterface;

abstract class Content extends \Granam\WebContentBuilder\Web\Content implements ContentInterface
{
    /** @var HtmlHelper */
    protected $htmlHelper;
    protected \DrdPlus\RulesSkeleton\Environment $environment;
    /** @var RulesBodyInterface */
    protected $body;

    public function __construct(HtmlHelper $htmlHelper, Environment $environment, HeadInterface $head, RulesBodyInterface $body)
    {
        parent::__construct($htmlHelper, $head, $body);
        $this->htmlHelper = $htmlHelper;
        $this->environment = $environment;
        $this->body = $body;
    }

    protected function buildHtmlDocument(string $content): HtmlDocument
    {
        $htmlDocument = new HtmlDocument($content);

        $this->body->preProcessDocument($htmlDocument);

        $this->processDocument($htmlDocument);

        return $this->body->postProcessDocument($htmlDocument);
    }

    protected function processDocument(HtmlDocument $htmlDocument)
    {
        $htmlDocument->body->classList->add('container');
        $this->solveIds($htmlDocument);
        $this->solveLinks($htmlDocument);
        $this->htmlHelper->injectIframesWithRemoteTables($htmlDocument);
        $this->htmlHelper->resolveDisplayMode($htmlDocument);
    }

    protected function solveIds(HtmlDocument $htmlDocument): void
    {
        $this->htmlHelper->addIdsToHeadings($htmlDocument);
        $this->htmlHelper->addIdsToTables($htmlDocument);
        $this->htmlHelper->unifyIds($htmlDocument);
        $this->htmlHelper->addAnchorsToIds($htmlDocument);
        $this->htmlHelper->replaceDiacriticsFromDrdPlusAnchorHashes($htmlDocument);
    }

    protected function solveLinks(HtmlDocument $htmlDocument): void
    {
        $this->htmlHelper->externalLinksTargetToBlank($htmlDocument);
        $this->htmlHelper->prepareSourceCodeLinks($htmlDocument);
        $this->htmlHelper->markExternalLinksByClass($htmlDocument);
        $this->htmlHelper->addVersionHashToAssets($htmlDocument);
    }
}
