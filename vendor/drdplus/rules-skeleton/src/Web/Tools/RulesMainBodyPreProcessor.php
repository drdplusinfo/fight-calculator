<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Tools;

use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Request;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;
use Granam\WebContentBuilder\HtmlDocument;

class RulesMainBodyPreProcessor extends StrictObject implements HtmlDocumentProcessorInterface
{
    private HtmlHelper $htmlHelper;
    private Request $request;

    public function __construct(HtmlHelper $htmlHelper, Request $request)
    {
        $this->htmlHelper = $htmlHelper;
        $this->request = $request;
    }

    public function processDocument(HtmlDocument $htmlDocument): HtmlDocument
    {
        $this->solveLocalLinksInTableOfContents($htmlDocument);
        $this->injectRouteBodyClass($htmlDocument);
        return $htmlDocument;
    }

    protected function solveLocalLinksInTableOfContents(HtmlDocument $htmlDocument)
    {
        $tableOfContents = $htmlDocument->getElementById((string)$this->htmlHelper::ID_TABLE_OF_CONTENTS);
        if (!$tableOfContents) {
            return;
        }
        foreach ($tableOfContents->getElementsByTagName('a') as $anchor) {
            $href = trim((string)$anchor->getAttribute('href'));
            if ($href !== '') {
                continue; // already defined anchors are not changed
            }
            $text = trim((string)$anchor->prop_get_innerHTML());
            if ($text === '') {
                continue; // no value to create anchor from
            }
            $anchor->setAttribute('href', "#$text");
        }
    }

    protected function injectRouteBodyClass(HtmlDocument $htmlDocument)
    {
        $htmlDocument->body->classList->add($this->getRouteBodyClass());
    }

    protected function getRouteBodyClass(): string
    {
        $sanitizedPath = StringTools::toConstantLikeValue($this->request->getRequestPath());
        if ($sanitizedPath === '' || $sanitizedPath === '_') {
            return HtmlHelper::CLASS_ROOT_PATH_ROUTE;
        }
        return HtmlHelper::CLASS_ROOTED_FROM_PATH_PREFIX . '_' . $sanitizedPath;
    }

}
