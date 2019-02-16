<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\Tests\CalculatorSkeleton\TestsConfiguration;

class TestsConfigurationTest extends \DrdPlus\Tests\CalculatorSkeleton\TestsConfigurationTest
{
    use Partials\AttackCalculatorTestTrait;

    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~(.+)Test$~'): string
    {
        return TestsConfiguration::class;
    }
}