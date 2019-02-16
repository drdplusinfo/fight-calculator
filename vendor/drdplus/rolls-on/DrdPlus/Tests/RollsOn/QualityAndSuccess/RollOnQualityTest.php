<?php
declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\QualityAndSuccess;

use Granam\DiceRolls\Roll;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use Granam\Tests\Tools\TestWithMockery;

class RollOnQualityTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $rollOnQuality = new RollOnQuality($preconditionsSum = 12345, $roll = $this->createRoll($rollValue = 56789));
        self::assertSame($preconditionsSum, $rollOnQuality->getPreconditionsSum());
        self::assertSame($roll, $rollOnQuality->getRoll());
        $expectedResult = $preconditionsSum + $rollValue;
        self::assertSame($expectedResult, $rollOnQuality->getValue());
        self::assertSame((string)$expectedResult, (String)$rollOnQuality);
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Roll
     */
    private function createRoll($value)
    {
        $roll = $this->mockery(Roll::class);
        $roll->shouldReceive('getValue')
            ->andReturn($value);

        return $roll;
    }
}
