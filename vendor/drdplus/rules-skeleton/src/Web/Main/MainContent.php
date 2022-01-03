<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Main;

use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Web\Content;
use Granam\WebContentBuilder\Web\HeadInterface;

class MainContent extends Content
{
    public function __construct(HtmlHelper $htmlHelper, Environment $environment, HeadInterface $head, MainBody $body)
    {
        parent::__construct($htmlHelper, $environment, $head, $body);
    }
}
