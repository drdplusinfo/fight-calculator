<?php declare(strict_types=1);

namespace Tests\DrdPlus\AttackSkeleton;

use Tests\DrdPlus\CalculatorSkeleton\TestsConfiguration;

class TestsConfigurationTest extends \Tests\DrdPlus\CalculatorSkeleton\TestsConfigurationTest
{
    use Partials\AttackCalculatorTestTrait;

    /**
     * @test
     * @dataProvider provideStrictBooleanConfiguration
     * @param string $directive
     * @param $value
     */
    public function Skeleton_boolean_tests_configuration_is_strict(string $directive, $value)
    {
        if (!$this->isSkeletonChecked()) {
            self::assertFalse(false, 'It is hard to test on real calculators');
            return;
        }
        parent::Skeleton_boolean_tests_configuration_is_strict($directive, $value);
    }

    /**
     * @test
     * @dataProvider provideStrictArrayConfiguration
     * @param string $directive
     * @param bool $hasContent
     */
    public function Skeleton_array_tests_configuration_is_strict(string $directive, bool $hasContent)
    {
        if (!$this->isSkeletonChecked()) {
            self::assertFalse(false, 'It is hard to test on real calculators');
            return;
        }
        parent::Skeleton_array_tests_configuration_is_strict($directive, $hasContent);
    }

    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~(.+)Test$~'): string
    {
        return TestsConfiguration::class;
    }
}
