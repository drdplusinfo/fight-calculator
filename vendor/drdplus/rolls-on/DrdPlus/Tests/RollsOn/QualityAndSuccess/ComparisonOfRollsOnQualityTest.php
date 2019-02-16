<?php
declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\QualityAndSuccess;

use DrdPlus\RollsOn\QualityAndSuccess\ComparisonOfRollsOnQuality;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use Granam\Tests\Tools\TestWithMockery;

class ComparisonOfRollsOnQualityTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideRollsOnQualityValueAndExpectedResult
     * @param $compareThatValue
     * @param $withThatValue
     * @param bool $firstIsLesser
     * @param bool $firstIsGreater
     */
    public function I_can_use_it($compareThatValue, $withThatValue, $firstIsLesser, $firstIsGreater)
    {
        $compareThat = $this->createRollOnQuality($compareThatValue);
        $withThat = $this->createRollOnQuality($withThatValue);

        self::assertSame($firstIsLesser, ComparisonOfRollsOnQuality::isLesser($compareThat, $withThat));
        self::assertSame($firstIsGreater, ComparisonOfRollsOnQuality::isGreater($compareThat, $withThat));
        self::assertSame(
            !$firstIsLesser && !$firstIsGreater,
            ComparisonOfRollsOnQuality::isEqual($compareThat, $withThat)
        );
        self::assertSame(
            $firstIsLesser ? -1 : ($firstIsGreater ? 1 : 0),
            ComparisonOfRollsOnQuality::compare($compareThat, $withThat)
        );
    }

    public function provideRollsOnQualityValueAndExpectedResult()
    {
        return [
            [1, 2, true, false],
            [2, 2, false, false],
            [3, 2, false, true],
        ];
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|RollOnQuality
     */
    private function createRollOnQuality($value)
    {
        $rollOnQuality = $this->mockery(RollOnQuality::class);
        $rollOnQuality->shouldReceive('getValue')
            ->andReturn($value);

        return $rollOnQuality;
    }
}
