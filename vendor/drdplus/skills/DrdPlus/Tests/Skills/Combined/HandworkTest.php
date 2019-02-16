<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Combined\Handwork;
use DrdPlus\Skills\Combined\RollsOnQuality\HandworkQuality;
use DrdPlus\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess\HandworkRollOnSuccess;
use Mockery\MockInterface;

class HandworkTest extends WithBonusToKnackFromCombinedTest
{
    /**
     * @test
     */
    public function I_can_create_handwork_quality_via_handwork()
    {
        $handwork = new Handwork($this->createProfessionLevel());
        $handworkQuality = $handwork->createHandworkQuality($this->createKnack(1), $this->createRoll2d6Plus(2));
        self::assertInstanceOf(HandworkQuality::class, $handworkQuality);
    }

    /**
     * @param int $value
     * @return Knack|MockInterface
     */
    private function createKnack(int $value): Knack
    {
        $knack = $this->mockery(Knack::class);
        $knack->shouldReceive('getValue')->andReturn($value);

        return $knack;
    }

    /**
     * @param int $value
     * @return Roll2d6DrdPlus|MockInterface
     */
    private function createRoll2d6Plus(int $value): Roll2d6DrdPlus
    {
        $roll2d6Plus = $this->mockery(Roll2d6DrdPlus::class);
        $roll2d6Plus->shouldReceive('getValue')->andReturn($value);
        $roll2d6Plus->shouldReceive('getRolledNumbers')->andReturn([$value]);

        return $roll2d6Plus;
    }

    /**
     * @test
     */
    public function I_can_create_roll_on_success_via_handwork()
    {
        $handwork = new Handwork($this->createProfessionLevel());
        $handworkRollOnSuccess = $handwork->createHandworkRollOnSuccess(
            $this->createKnack(1),
            $this->createRoll2d6Plus(2),
            123
        );
        self::assertInstanceOf(HandworkRollOnSuccess::class, $handworkRollOnSuccess);
    }
}