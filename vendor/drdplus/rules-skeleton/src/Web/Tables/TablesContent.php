<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Tables;

use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Web\Content;
use Granam\WebContentBuilder\Web\HeadInterface;

class TablesContent extends Content
{
    public function __construct(HtmlHelper $htmlHelper, Environment $environment, HeadInterface $head, TablesBody $tablesBody)
    {
        parent::__construct($htmlHelper, $environment, $head, $tablesBody);
    }

}
