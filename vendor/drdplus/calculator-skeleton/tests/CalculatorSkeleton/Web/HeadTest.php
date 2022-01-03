<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton\Web;

use DrdPlus\RulesSkeleton\Web\Head;
use Tests\DrdPlus\CalculatorSkeleton\Partials\CalculatorContentTestTrait;

class HeadTest extends \Tests\DrdPlus\RulesSkeleton\Web\HeadTest
{
    use CalculatorContentTestTrait;

    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~\\\Tests(.+)Test$~'): string
    {
        return Head::class;
    }
}
