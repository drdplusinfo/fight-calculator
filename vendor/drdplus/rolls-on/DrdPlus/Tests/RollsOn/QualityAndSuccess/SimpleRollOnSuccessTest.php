<?php declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\QualityAndSuccess;

use Granam\DiceRolls\Roll;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\RollsOn\QualityAndSuccess\SimpleRollOnSuccess;
use Granam\Tests\Tools\TestWithMockery;

class SimpleRollOnSuccessTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $successfulRollOn = new SimpleRollOnSuccess(
            $difficulty = 123,
            $rollOnQuality = new RollOnQuality($preconditions = 456, $this->createRoll($rollValue = 789))
        );
        self::assertSame($difficulty, $successfulRollOn->getDifficulty());
        self::assertSame($rollOnQuality, $successfulRollOn->getRollOnQuality());
        self::assertGreaterThan($difficulty, $preconditions + $rollValue);
        self::assertSame('success', $successfulRollOn->getResult());

        $failedRollOn = new SimpleRollOnSuccess(
            $difficulty = 789,
            $rollOnQuality = new RollOnQuality($preconditions = 456, $this->createRoll($rollValue = 123))
        );
        self::assertSame($difficulty, $failedRollOn->getDifficulty());
        self::assertSame($rollOnQuality, $failedRollOn->getRollOnQuality());
        self::assertLessThan($difficulty, $preconditions + $rollValue);
        self::assertSame('failure', $failedRollOn->getResult());

        $withCustomSuccessCode = new SimpleRollOnSuccess(
            $difficulty = 123,
            $rollOnQuality = new RollOnQuality($preconditions = 456, $this->createRoll($rollValue = 789)),
            $successCode = 'Hurray!'
        );
        self::assertSame($difficulty, $withCustomSuccessCode->getDifficulty());
        self::assertSame($rollOnQuality, $withCustomSuccessCode->getRollOnQuality());
        self::assertGreaterThan($difficulty, $preconditions + $rollValue);
        self::assertSame($successCode, $withCustomSuccessCode->getResult());

        $withCustomFailureCode = new SimpleRollOnSuccess(
            $difficulty = 789,
            $rollOnQuality = new RollOnQuality($preconditions = 456, $this->createRoll($rollValue = 123)),
            'foo',
            $failureCode = 'What the...'
        );
        self::assertSame($difficulty, $withCustomFailureCode->getDifficulty());
        self::assertSame($rollOnQuality, $withCustomFailureCode->getRollOnQuality());
        self::assertLessThan($difficulty, $preconditions + $rollValue);
        self::assertSame($failureCode, $withCustomFailureCode->getResult());
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Roll
     */
    protected function createRoll($value): Roll
    {
        $roll = $this->mockery(Roll::class);
        $roll->shouldReceive('getValue')
            ->andReturn($value);

        return $roll;
    }
}
