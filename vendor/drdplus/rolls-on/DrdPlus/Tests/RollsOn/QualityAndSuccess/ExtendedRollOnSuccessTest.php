<?php
declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\QualityAndSuccess;

use Granam\DiceRolls\Roll;
use DrdPlus\RollsOn\QualityAndSuccess\BasicRollOnSuccess;
use DrdPlus\RollsOn\QualityAndSuccess\ExtendedRollOnSuccess;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\RollsOn\QualityAndSuccess\SimpleRollOnSuccess;
use Granam\Tests\Tools\TestWithMockery;

class ExtendedRollOnSuccessTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideSimpleRollsOnSuccessAndResult
     * @param RollOnQuality $expectedRollOnQuality
     * @param array $simpleRollsOnSuccess
     * @param string $expectedResultCode
     * @param bool $expectingSuccess
     */
    public function I_can_use_it(RollOnQuality $expectedRollOnQuality, array $simpleRollsOnSuccess, $expectedResultCode, $expectingSuccess)
    {
        $extendedRollOnSuccessReflection = new \ReflectionClass(ExtendedRollOnSuccess::class);
        /** @var ExtendedRollOnSuccess $extendedRollOnSuccess */
        $extendedRollOnSuccess = $extendedRollOnSuccessReflection->newInstanceArgs($simpleRollsOnSuccess);

        self::assertSame($expectedRollOnQuality, $extendedRollOnSuccess->getRollOnQuality());
        self::assertSame($expectedResultCode, $extendedRollOnSuccess->getResult());
        self::assertSame($expectedResultCode, (string)$extendedRollOnSuccess);
        if ($expectingSuccess) {
            self::assertFalse($extendedRollOnSuccess->isFailure());
            self::assertTrue($extendedRollOnSuccess->isSuccess());
        } else {
            self::assertTrue($extendedRollOnSuccess->isFailure());
            self::assertFalse($extendedRollOnSuccess->isSuccess());
        }
    }

    public function provideSimpleRollsOnSuccessAndResult()
    {
        return [
            [ // from single simple roll on success
                $rollOnQuality = $this->createRollOnQuality(5 /* roll value */),
                [
                    $this->createSimpleRollOnSuccess(9 /* difficulty */, $rollOnQuality, false /* unsuccessful */, 'what happened?'),
                ],
                'what happened?', /* result code */
                false /* expecting failure */
            ],
            [ // from simple roll on success and basic roll on success (which is simple roll also)
                $rollOnQuality = $this->createRollOnQuality(3 /* roll value */),
                [
                    $this->createSimpleRollOnSuccess(2 /* difficulty */, $rollOnQuality),
                    $this->createBasicRollOnSuccess(5 /* difficulty */, $rollOnQuality, true /* is successful */, 'hurray'),
                ],
                'hurray',
                true /* expecting success */
            ],
            [ // from more than three simple rolls on success with two successful and non-sequential difficulty
                $rollOnQuality = $this->createRollOnQuality(2 /* roll value */),
                [
                    $this->createSimpleRollOnSuccess(5 /* difficulty */, $rollOnQuality),
                    $this->createSimpleRollOnSuccess(1 /* difficulty */, $rollOnQuality, true /* success */, 'success'),
                    $this->createSimpleRollOnSuccess(3 /* difficulty */, $rollOnQuality),
                    $this->createSimpleRollOnSuccess(2 /* difficulty */, $rollOnQuality, true /* success */, 'better success'),
                ],
                'better success',
                true /* expecting success */
            ],
            [ // from more than three simple rolls on success without success at all and non-sequential difficulty
                $rollOnQuality = $this->createRollOnQuality(2 /* roll value */),
                [
                    $this->createSimpleRollOnSuccess(5 /* difficulty */, $rollOnQuality, false, 'I failed against 5'),
                    $this->createSimpleRollOnSuccess(1 /* difficulty */, $rollOnQuality, false, 'I failed against 1'),
                    $this->createSimpleRollOnSuccess(3 /* difficulty */, $rollOnQuality, false, 'I failed against 3'),
                    $this->createSimpleRollOnSuccess(2 /* difficulty */, $rollOnQuality, false, 'I failed against 2'),
                ],
                'I failed against 1', // the lowest difficulty is used as a result
                false /* expecting failure */
            ],
        ];
    }

    /**
     * @param $difficulty
     * @param RollOnQuality $rollOnQuality
     * @param $isSuccess
     * @param $resultValue
     * @return \Mockery\MockInterface|SimpleRollOnSuccess
     */
    private function createSimpleRollOnSuccess($difficulty, RollOnQuality $rollOnQuality, $isSuccess = false, $resultValue = 'foo')
    {
        return $this->createRollOnSuccess(SimpleRollOnSuccess::class, $difficulty, $rollOnQuality, $isSuccess, $resultValue);
    }

    private function createRollOnSuccess($class, $difficulty, RollOnQuality $rollOnQuality, $isSuccess, $resultValue)
    {
        $rollOnSuccess = $this->mockery($class);
        $rollOnSuccess->shouldReceive('getDifficulty')
            ->andReturn($difficulty);
        $rollOnSuccess->shouldReceive('isSuccess')
            ->andReturn($isSuccess);
        $rollOnSuccess->shouldReceive('getResult')
            ->andReturn($resultValue);
        $rollOnSuccess->shouldReceive('getRollOnQuality')
            ->andReturn($rollOnQuality);

        return $rollOnSuccess;
    }

    /**
     * @param $value
     * @param $rollValue
     * @param array $rolledNumbers
     * @return \Mockery\MockInterface|RollOnQuality
     */
    private function createRollOnQuality($value, int $rollValue = 999, array $rolledNumbers = ['some', 'rolled', 'numbers'])
    {
        $rollOnQuality = $this->mockery(RollOnQuality::class);
        $rollOnQuality->shouldReceive('getPreconditionsSum')
            ->andReturn(123);
        $rollOnQuality->shouldReceive('getValue')
            ->andReturn($value);
        $rollOnQuality->shouldReceive('getRoll')
            ->andReturn($roll = $this->mockery(Roll::class));
        $roll->shouldReceive('getValue')
            ->andReturn($rollValue);
        $roll->shouldReceive('getRolledNumbers')
            ->andReturn($rolledNumbers);

        return $rollOnQuality;
    }

    /**
     * @param $difficulty
     * @param RollOnQuality $rollOnQuality
     * @param bool $isSuccess
     * @param string $resultValue
     * @return \Mockery\MockInterface|BasicRollOnSuccess
     */
    private function createBasicRollOnSuccess($difficulty, RollOnQuality $rollOnQuality, $isSuccess = false, $resultValue = 'foo')
    {
        return $this->createRollOnSuccess(BasicRollOnSuccess::class, $difficulty, $rollOnQuality, $isSuccess, $resultValue);
    }

    /**
     * @test
     * @expectedException \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\ExpectedSimpleRollsOnSuccessOnly
     */
    public function I_can_create_it_only_from_simple_rolls_on_success()
    {
        $rollOnQuality = $this->createRollOnQuality(123);

        new ExtendedRollOnSuccess(
            $this->createSimpleRollOnSuccess(1, $rollOnQuality),
            $this->createBasicRollOnSuccess(2, $rollOnQuality),
            $this->createSimpleRollOnSuccess(3, $rollOnQuality),
            new ExtendedRollOnSuccess($this->createSimpleRollOnSuccess(23, $rollOnQuality))
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\EveryDifficultyShouldBeUnique
     */
    public function I_can_use_only_unique_difficulties()
    {
        $rollOnQuality = $this->createRollOnQuality(123);

        new ExtendedRollOnSuccess(
            $this->createSimpleRollOnSuccess(1, $rollOnQuality),
            $this->createBasicRollOnSuccess(1, $rollOnQuality)
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\EverySuccessCodeShouldBeUnique
     */
    public function I_can_use_only_unique_success_codes()
    {
        $rollOnQuality = $this->createRollOnQuality(1);

        new ExtendedRollOnSuccess(
            $this->createSimpleRollOnSuccess(1, $rollOnQuality, true, 'success'),
            $this->createBasicRollOnSuccess(2, $rollOnQuality, true, 'success')
        );
    }

    /**
     * @test
     */
    public function I_can_use_non_unique_success_codes_if_no_success_happens_on_them()
    {
        $rollOnQuality = $this->createRollOnQuality(1);

        $extendedRollOnSuccess = new ExtendedRollOnSuccess(
            $this->createSimpleRollOnSuccess(1, $rollOnQuality, true, 'success'),
            $this->createBasicRollOnSuccess(2, $rollOnQuality, false /* failure for this roll */, 'success' /* code used only on success */)
        );
        self::assertTrue($extendedRollOnSuccess->isSuccess());
    }

    /**
     * @test
     */
    public function I_can_use_different_instances_with_same_values_of_rolls_on_quality()
    {
        $firstRollOnQuality = $this->createRollOnQuality(1);
        $similarRollOnQuality = $this->createRollOnQuality(1);
        $extendedRollOnSuccess = new ExtendedRollOnSuccess(
            $this->createSimpleRollOnSuccess(1, $firstRollOnQuality),
            $this->createBasicRollOnSuccess(2, $similarRollOnQuality)
        );
        self::assertEquals($similarRollOnQuality, $extendedRollOnSuccess->getRollOnQuality());
    }

    /**
     * @test
     * @dataProvider provideSimpleRollsWithDifferentRollsOnQuality
     * @expectedException \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\RollOnQualityHasToBeTheSame
     * @param SimpleRollOnSuccess $firstSimpleRoll
     * @param SimpleRollOnSuccess $secondSimpleRoll
     */
    public function I_can_not_use_different_rolls_on_quality(SimpleRollOnSuccess $firstSimpleRoll, SimpleRollOnSuccess $secondSimpleRoll)
    {
        new ExtendedRollOnSuccess($firstSimpleRoll, $secondSimpleRoll);
    }

    public function provideSimpleRollsWithDifferentRollsOnQuality()
    {
        return [
            [ // different roll on quality value
                $this->createSimpleRollOnSuccess(5, $this->createRollOnQuality(1)),
                $this->createSimpleRollOnSuccess(9, $this->createRollOnQuality(2)),
            ],
            [ // different roll on quality roll value
                $this->createSimpleRollOnSuccess(5, $this->createRollOnQuality(1, 1)),
                $this->createSimpleRollOnSuccess(9, $this->createRollOnQuality(1, 2)),
            ],
            [ // different roll on quality rolled numbers
                $this->createSimpleRollOnSuccess(5, $this->createRollOnQuality(1, 2, [1, 2])),
                $this->createSimpleRollOnSuccess(9, $this->createRollOnQuality(1, 2, [1, 3])),
            ],
        ];
    }
}
