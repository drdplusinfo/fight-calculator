<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton\Configurations;

use DrdPlus\RulesSkeleton\Configurations\Dirs;
use Tests\DrdPlus\CalculatorSkeleton\Partials\CalculatorContentTestTrait;

class DirsTest extends \Tests\DrdPlus\RulesSkeleton\Configurations\DirsTest
{
    use CalculatorContentTestTrait;

    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~\\\Tests(.+)Test$~'): string
    {
        return Dirs::class;
    }
}
