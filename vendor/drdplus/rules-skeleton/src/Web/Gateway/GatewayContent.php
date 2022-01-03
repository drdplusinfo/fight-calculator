<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Gateway;

use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Web\Content;
use Granam\WebContentBuilder\HtmlDocument;
use Granam\WebContentBuilder\Web\HeadInterface;

class GatewayContent extends Content
{
    public function __construct(HtmlHelper $htmlHelper, Environment $environment, HeadInterface $head, GatewayBody $gatewayBody)
    {
        parent::__construct($htmlHelper, $environment, $head, $gatewayBody);
    }

    protected function processDocument(HtmlDocument $htmlDocument)
    {
        $htmlDocument->body->classList->add('container');
        $this->solveIds($htmlDocument);
        $this->solveLinks($htmlDocument);
    }

    protected function solveIds(HtmlDocument $htmlDocument): void
    {
        $this->htmlHelper->unifyIds($htmlDocument);
        $this->htmlHelper->replaceDiacriticsFromDrdPlusAnchorHashes($htmlDocument);
    }

    protected function solveLinks(HtmlDocument $htmlDocument): void
    {
        $this->htmlHelper->externalLinksTargetToBlank($htmlDocument);
        $this->htmlHelper->markExternalLinksByClass($htmlDocument);
        $this->htmlHelper->addVersionHashToAssets($htmlDocument);
    }
}
