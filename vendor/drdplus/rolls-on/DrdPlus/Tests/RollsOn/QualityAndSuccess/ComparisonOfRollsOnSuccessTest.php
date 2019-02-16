<?php
declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\QualityAndSuccess;

use DrdPlus\RollsOn\QualityAndSuccess\ComparisonOfRollsOnSuccess;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnSuccess;
use Granam\Tests\Tools\TestWithMockery;

class ComparisonOfRollsOnSuccessTest extends TestWithMockery
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
        $compareThat = $this->createRollOnSuccessWithQuality($compareThatValue);
        $withThat = $this->createRollOnSuccessWithQuality($withThatValue);

        self::assertSame($firstIsLesser, ComparisonOfRollsOnSuccess::isLesser($compareThat, $withThat));
        self::assertSame($firstIsGreater, ComparisonOfRollsOnSuccess::isGreater($compareThat, $withThat));
        self::assertSame(
            !$firstIsLesser && !$firstIsGreater,
            ComparisonOfRollsOnSuccess::isEqual($compareThat, $withThat)
        );
        self::assertSame(
            $firstIsLesser ? -1 : ($firstIsGreater ? 1 : 0),
            ComparisonOfRollsOnSuccess::compare($compareThat, $withThat)
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
     * @param $rollOnQualityValue
     * @return \Mockery\MockInterface|RollOnSuccess
     */
    private function createRollOnSuccessWithQuality($rollOnQualityValue)
    {
        $rollOnSuccess = $this->mockery(RollOnSuccess::class);
        $rollOnSuccess->shouldReceive('getRollOnQuality')
            ->andReturn($rollOnQuality = $this->mockery(RollOnQuality::class));
        $rollOnQuality->shouldReceive('getValue')
            ->andReturn($rollOnQualityValue);

        return $rollOnSuccess;
    }
}
