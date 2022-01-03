<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\NotFound;

use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Web\Content;
use Granam\WebContentBuilder\HtmlDocument;
use Granam\WebContentBuilder\Web\HeadInterface;

class NotFoundContent extends Content
{
    public function __construct(HtmlHelper $htmlHelper, Environment  $environment, HeadInterface $head, NotFoundBody $body)
    {
        parent::__construct($htmlHelper, $environment, $head, $body);
    }

    protected function solveIds(HtmlDocument $htmlDocument): void
    {
        $this->htmlHelper->addIdsToTables($htmlDocument);
        $this->htmlHelper->unifyIds($htmlDocument);
        $this->htmlHelper->addAnchorsToIds($htmlDocument);
        $this->htmlHelper->replaceDiacriticsFromDrdPlusAnchorHashes($htmlDocument);
    }

}
