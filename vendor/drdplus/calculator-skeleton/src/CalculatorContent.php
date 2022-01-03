<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton;

use DrdPlus\RulesSkeleton\Cache\Cache;
use DrdPlus\RulesSkeleton\Web\Main\MainContent;
use DrdPlus\RulesSkeleton\Web\RulesContent;
use DrdPlus\RulesSkeleton\Web\RulesHtmlDocumentPostProcessor;

class CalculatorContent extends RulesContent
{
    public function __construct(
        MainContent $calculatorMainContent,
        Cache $cache,
        RulesHtmlDocumentPostProcessor $rulesHtmlDocumentPostProcessor
    )
    {
        parent::__construct($calculatorMainContent, $cache, $rulesHtmlDocumentPostProcessor, self::FULL, null);
    }
}
