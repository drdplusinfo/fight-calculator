<?php
declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\QualityAndSuccess;

use Granam\DiceRolls\Roll;
use DrdPlus\RollsOn\QualityAndSuccess\Requirements\AnimalDefiance;
use DrdPlus\RollsOn\QualityAndSuccess\Requirements\PreviousFailuresCount;
use DrdPlus\RollsOn\QualityAndSuccess\Requirements\Ride;
use DrdPlus\RollsOn\QualityAndSuccess\Requirements\RidingSkill;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnAnimalControl;
use DrdPlus\RollsOn\Traps\RollOnAgility;
use Granam\Tests\Tools\TestWithMockery;

class RollOnAnimalControlTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $rollOnAnimalControl = new RollOnAnimalControl(
            $rollOnAgility = $this->createRollOnAgility(321),
            $this->createAnimalDefiance(123),
            $this->createRide(456),
            $this->createRidingSkill(789),
            $this->createPreviousFailuresCount(987)
        );

        self::assertSame($rollOnAgility, $rollOnAnimalControl->getRollOnAgility());
        self::assertSame($rollOnAgility, $rollOnAnimalControl->getRollOnQuality());
    }

    /**
     * @param $rollValue
     * @return \Mockery\MockInterface|RollOnAgility
     */
    private function createRollOnAgility($rollValue)
    {
        $rollOnAgility = $this->mockery(RollOnAgility::class);
        $rollOnAgility->shouldReceive('getValue')
            ->andReturn($rollValue);
        $rollOnAgility->shouldReceive('getPreconditionsSum')
            ->andReturn(67890 /* whatever */);
        $rollOnAgility->shouldReceive('getRoll')
            ->andReturn($roll = $this->mockery(Roll::class));
        $roll->shouldReceive('getValue')
            ->andReturn(12345 /* whatever */);
        $roll->shouldReceive('getRolledNumbers')
            ->andReturn([1, 3, 4]);

        return $rollOnAgility;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|AnimalDefiance
     */
    private function createAnimalDefiance($value)
    {
        $animalDefiance = $this->mockery(AnimalDefiance::class);
        $animalDefiance->shouldReceive('getValue')
            ->andReturn($value);

        return $animalDefiance;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Ride
     */
    private function createRide($value)
    {
        $ride = $this->mockery(Ride::class);
        $ride->shouldReceive('getValue')
            ->andReturn($value);

        return $ride;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|RidingSkill
     */
    private function createRidingSkill($value)
    {
        $ridingSkill = $this->mockery(RidingSkill::class);
        $ridingSkill->shouldReceive('getValue')
            ->andReturn($value);

        return $ridingSkill;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|PreviousFailuresCount
     */
    private function createPreviousFailuresCount($value)
    {
        $previousFailuresCount = $this->mockery(PreviousFailuresCount::class);
        $previousFailuresCount->shouldReceive('getValue')
            ->andReturn($value);

        return $previousFailuresCount;
    }

    /**
     * @test
     * @dataProvider provideValuesToModerateFail
     * @param $roll
     * @param $defiance
     * @param $ride
     * @param $ridingSkill
     * @param $isModerateFailure
     * @param $isSuccess
     * @param $previousFailuresCount
     */
    public function I_can_find_out_if_failed_just_moderately(
        $roll,
        $defiance,
        $ride,
        $ridingSkill,
        $previousFailuresCount,
        $isModerateFailure,
        $isSuccess
    )
    {
        $rollOnAnimalControl = new RollOnAnimalControl(
            $this->createRollOnAgility($roll),
            $this->createAnimalDefiance($defiance),
            $this->createRide($ride),
            $this->createRidingSkill($ridingSkill),
            $this->createPreviousFailuresCount($previousFailuresCount)
        );

        self::assertSame($isModerateFailure, $rollOnAnimalControl->isModerateFailure());
        self::assertSame($isSuccess, $rollOnAnimalControl->isSuccess());
        self::assertSame(!$isSuccess, $rollOnAnimalControl->isFailure());
    }

    public function provideValuesToModerateFail()
    {
        return [
            [10, 5, 5, 0, 0, false, true],
            [10, 5, 5, 0, 1, true, false],
            [10, 5, 6, 1, 0, false, true],
            [10, 5, 6, 1, 2, true, false],
            [10, 5, 6, 0, 0, true, false], // in case of riding on animal even partial failure is failure
            [3, 3, 5, 0, 0, false, false],
            [3, 3, 5, 0, 5, false, false],
            [3, 3, 5, 2, 0, true, false],
            [3, 3, 5, 50, 2, false, true],
            [3, 3, 5, 50, 200, false, false],
        ];
    }

    /**
     * @test
     * @dataProvider provideValuesToFatalFail
     * @param $roll
     * @param $defiance
     * @param $ride
     * @param $ridingSkill
     * @param $previousFailuresCount
     * @param $isFatalFailure
     */
    public function I_can_find_out_if_failed_fatally($roll, $defiance, $ride, $ridingSkill, $previousFailuresCount, $isFatalFailure)
    {
        $rollOnAnimalControl = new RollOnAnimalControl(
            $this->createRollOnAgility($roll),
            $this->createAnimalDefiance($defiance),
            $this->createRide($ride),
            $this->createRidingSkill($ridingSkill),
            $this->createPreviousFailuresCount($previousFailuresCount)
        );

        self::assertSame($isFatalFailure, $rollOnAnimalControl->isFatalFailure());
    }

    public function provideValuesToFatalFail()
    {
        return [
            [10, 5, 5, 0, 4, false],
            [10, 5, 5, 0, 5, true],
            [10, 9, 6, 0, 0, true],
            [10, 9, 6, 1, 0, false],
            [10, 5, 6, 1, 5, true],
            [10, 15, 6, 999, 3, false],
        ];
    }
}
