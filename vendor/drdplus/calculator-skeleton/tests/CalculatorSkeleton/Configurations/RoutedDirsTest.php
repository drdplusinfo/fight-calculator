<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton\Configurations;

use DrdPlus\RulesSkeleton\Configurations\RoutedDirs;
use Tests\DrdPlus\CalculatorSkeleton\Partials\CalculatorContentTestTrait;

class RoutedDirsTest extends \Tests\DrdPlus\RulesSkeleton\Configurations\RoutedDirsTest
{
    use CalculatorContentTestTrait;

    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~\\\Tests(.+)Test$~'): string
    {
        return RoutedDirs::class;
    }
}
