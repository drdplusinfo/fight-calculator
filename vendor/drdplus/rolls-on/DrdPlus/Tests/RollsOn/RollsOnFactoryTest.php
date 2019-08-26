<?php declare(strict_types=1);

namespace DrdPlus\RollsOn;

use Granam\DiceRolls\Roll;
use Granam\DiceRolls\Roller;
use Granam\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Will;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use Granam\Tests\Tools\TestWithMockery;

class RollsOnFactoryTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_make_roll_on_fight(): void
    {
        $rollsOn = new RollsOnFactory($roller = $this->createRoller2d6DrdPlus($rollValue = 123));
        $rollOnFight = $rollsOn->makeRollOnFight($fightNumber = 55667788);
        self::assertSame($fightNumber, $rollOnFight->getFightNumber());
        self::assertSame($fightNumber + $rollValue, $rollOnFight->getValue());
    }

    /**
     * @param $rollValue
     * @return \Mockery\MockInterface|Roller2d6DrdPlus
     */
    private function createRoller2d6DrdPlus($rollValue = false)
    {
        $roller2d6DrdPlus = $this->mockery(Roller2d6DrdPlus::class);
        $roller2d6DrdPlus->shouldReceive('roll')
            ->andReturn($roll2d6DrdPlus = $this->mockery(Roll2d6DrdPlus::class));
        if ($rollValue !== false) {
            $roll2d6DrdPlus->shouldReceive('getValue')
                ->andReturn($rollValue);
            $roll2d6DrdPlus->shouldReceive('getRolledNumbers')
                ->andReturn(['foo']);
        }

        return $roller2d6DrdPlus;
    }

    /**
     * @test
     */
    public function I_can_make_roll_on_quality()
    {
        $rollsOn = new RollsOnFactory($this->createRoller2d6DrdPlus());

        $rollOnQuality = $rollsOn->makeRollOnQuality(
            $preconditionsSum = 123,
            $roller = $this->createRoller($roll = $this->createRoll($rollValue = 456))
        );
        self::assertSame($preconditionsSum, $rollOnQuality->getPreconditionsSum());
        self::assertSame($roll, $rollOnQuality->getRoll());
        $expectedResult = $preconditionsSum + $rollValue;
        self::assertSame($expectedResult, $rollOnQuality->getValue());
        self::assertSame((string)$expectedResult, (string)$rollOnQuality->getValue());
    }

    /**
     * @test
     */
    public function I_can_make_basic_roll_on_success()
    {
        $rollsOn = new RollsOnFactory($this->createRoller2d6DrdPlus());

        $basicRollOnSuccess = $rollsOn->makeBasicRollOnSuccess(
            $difficulty = 123,
            $preconditionsSum = 456,
            $this->createRoller($roll = $this->createRoll())
        );
        self::assertSame($difficulty, $basicRollOnSuccess->getDifficulty());
        self::assertInstanceOf(RollOnQuality::class, $rollOnQuality = $basicRollOnSuccess->getRollOnQuality());

        self::assertSame($roll, $rollOnQuality->getRoll());
    }

    /**
     * @param $roll
     * @return \Mockery\MockInterface|Roller
     */
    private function createRoller($roll)
    {
        $roller = $this->mockery(Roller::class);
        $roller->shouldReceive('roll')
            ->andReturn($roll);

        return $roller;
    }

    /**
     * @param $rollValue
     * @return \Mockery\MockInterface|Roll
     */
    private function createRoll($rollValue = false)
    {
        $roll = $this->mockery(Roll::class);
        if ($rollValue !== false) {
            $roll->shouldReceive('getValue')
                ->andReturn($rollValue);
        }

        return $roll;
    }

    /**
     * @test
     * @dataProvider provideMalusRollAndWill
     * @param $rollValue
     * @param $willValue
     * @param $expectedMalus
     */
    public function I_can_make_malus_roll_on_will($rollValue, $willValue, $expectedMalus)
    {
        $rollsOnFactory = new RollsOnFactory($this->createRoller2d6DrdPlus($rollValue));

        $malusRollOnWill = $rollsOnFactory->makeMalusRollOnWill(Will::getIt($willValue));
        self::assertSame($expectedMalus, $malusRollOnWill->getMalusValue());
        self::assertSame($malusRollOnWill->getRollOnQuality(), $malusRollOnWill->getRollOnWill());
        self::assertInstanceOf(RollOnQuality::class, $rollOnQuality = $malusRollOnWill->getRollOnQuality());
    }

    public function provideMalusRollAndWill(): array
    {
        return [
            [2, 2, -3],
            [3, 2, -2],
            [2, 3, -2],
            [6, 4, -1],
            [7, 8, 0],
            [14, 1, 0],
        ];
    }

}
