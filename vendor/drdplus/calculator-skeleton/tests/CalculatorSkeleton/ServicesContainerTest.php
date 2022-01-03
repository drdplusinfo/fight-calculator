<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton;

use DrdPlus\RulesSkeleton\ServicesContainer;

class ServicesContainerTest extends \Tests\DrdPlus\RulesSkeleton\ServicesContainerTest
{
    use Partials\CalculatorContentTestTrait;

    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~\\\Tests(.+)Test$~'): string
    {
        return ServicesContainer::class;
    }
}
