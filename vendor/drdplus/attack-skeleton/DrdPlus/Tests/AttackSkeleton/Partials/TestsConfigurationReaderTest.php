<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Partials;

use DrdPlus\Tests\CalculatorSkeleton\Partials\TestsConfigurationReader;

class TestsConfigurationReaderTest extends \DrdPlus\Tests\CalculatorSkeleton\Partials\TestsConfigurationReaderTest
{
    use AttackCalculatorTestTrait;

    protected function getTestsConfigurationReaderClass(): string
    {
        return TestsConfigurationReader::class;
    }
}