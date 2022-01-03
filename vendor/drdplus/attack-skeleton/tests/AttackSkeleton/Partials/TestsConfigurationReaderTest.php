<?php declare(strict_types=1);

namespace Tests\DrdPlus\AttackSkeleton\Partials;

use Tests\DrdPlus\CalculatorSkeleton\Partials\TestsConfigurationReader;

class TestsConfigurationReaderTest extends \Tests\DrdPlus\CalculatorSkeleton\Partials\TestsConfigurationReaderTest
{
    use AttackCalculatorTestTrait;

    protected function getTestsConfigurationReaderClass(): string
    {
        return TestsConfigurationReader::class;
    }
}
