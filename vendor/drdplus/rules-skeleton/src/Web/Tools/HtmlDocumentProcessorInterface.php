<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Tools;

use Granam\WebContentBuilder\HtmlDocument;

interface HtmlDocumentProcessorInterface
{
    public function processDocument(HtmlDocument $htmlDocument): HtmlDocument;
}
