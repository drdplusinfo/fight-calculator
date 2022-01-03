<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Tools;

use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Request;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\HtmlDocument;

class TablesBodyPostProcessor extends StrictObject implements HtmlDocumentProcessorInterface
{
    private \DrdPlus\RulesSkeleton\Request $request;
    private \DrdPlus\RulesSkeleton\HtmlHelper $htmlHelper;

    public function __construct(Request $request, HtmlHelper $htmlHelper)
    {
        $this->request = $request;
        $this->htmlHelper = $htmlHelper;
    }

    public function processDocument(HtmlDocument $htmlDocument): HtmlDocument
    {
        $backgroundRelatedElements = $this->htmlHelper->findBackgroundRelatedElements($htmlDocument);
        $tablesWithIds = $this->htmlHelper->findTablesWithIds($htmlDocument, $this->request->getRequestedTablesIds());
        $tablesRelatedElements = $this->htmlHelper->findTablesRelatedElements($htmlDocument);

        foreach ($htmlDocument->body->children as $child) {
            $child->remove();
        }

        foreach ($backgroundRelatedElements as $backgroundRelatedElement) {
            $htmlDocument->body->appendChild($backgroundRelatedElement);
        }
        foreach ($tablesWithIds as $table) {
            $htmlDocument->body->appendChild($table);
        }
        foreach ($tablesRelatedElements as $tablesRelatedElement) {
            $htmlDocument->body->appendChild($tablesRelatedElement);
        }

        return $htmlDocument;
    }

}
