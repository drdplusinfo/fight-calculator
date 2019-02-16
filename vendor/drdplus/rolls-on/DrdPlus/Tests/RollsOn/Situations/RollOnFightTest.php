<?php
declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\Situations;

use DrdPlus\RollsOn\Situations\RollOnFight;

class RollOnFightTest extends RollOnSituationTest
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $rollOnFight = new RollOnFight($fightNumber = 123, $roll2d6DrdPlus = $this->createRoll2d6DrdPlus($rollValue = 456));
        self::assertSame($fightNumber, $rollOnFight->getFightNumber());
        self::assertSame($roll2d6DrdPlus, $rollOnFight->getRoll2d6Plus());
        $expectedRollOnFightValue = $fightNumber + $rollValue;
        self::assertSame($expectedRollOnFightValue, $rollOnFight->getValue());
        self::assertSame((string)$expectedRollOnFightValue, (string)$rollOnFight);
    }

}
