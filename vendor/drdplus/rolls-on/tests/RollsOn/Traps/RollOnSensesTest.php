<?php declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Properties\Derived\Senses;
use DrdPlus\BaseProperties\Property;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\RollsOn\Traps\BonusFromUsedRemarkableSense;
use DrdPlus\RollsOn\Traps\RollOnSenses;

class RollOnSensesTest extends RollOnQualityTest
{
    /**
     * @param Property $property
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @return RollOnSenses|RollOnQuality
     */
    protected function createSutInstance(Property $property, Roll2d6DrdPlus $roll2D6DrdPlus): RollOnQuality
    {
        self::assertInstanceOf(Senses::class, $property);

        /** @var Senses $property */
        return new RollOnSenses($property, $roll2D6DrdPlus, $this->createBonusFromUsedRemarkableSense(0));
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|BonusFromUsedRemarkableSense
     */
    private function createBonusFromUsedRemarkableSense($value)
    {
        $bonusFromUsedRemarkableSense = $this->mockery(BonusFromUsedRemarkableSense::class);
        $bonusFromUsedRemarkableSense->shouldReceive('getValue')
            ->andReturn($value);
        $bonusFromUsedRemarkableSense->shouldReceive('__toString')
            ->andReturn((string)$value);

        return $bonusFromUsedRemarkableSense;
    }

    /**
     * @test
     */
    public function I_can_affect_it_by_bonus_from_used_remarkable_sense()
    {
        $rollOnSenses = new RollOnSenses(
            $senses = $this->createSenses(123),
            $roll2D6DrdPlus = $this->createRoll2d6DrdPlus(456),
            $bonusFromUsedRemarkableSense = $this->createBonusFromUsedRemarkableSense(789)
        );
        self::assertSame(123 + 456 + 789, $rollOnSenses->getValue());
        self::assertSame($senses, $rollOnSenses->getSenses());
        self::assertSame($roll2D6DrdPlus, $rollOnSenses->getRoll());
        self::assertSame($bonusFromUsedRemarkableSense, $rollOnSenses->getBonusFromUsedRemarkableSense());
        self::assertSame(123 + 456, $rollOnSenses->getValueWithoutBonusFromUsedRemarkableSense());
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Senses
     */
    private function createSenses($value)
    {
        $senses = $this->mockery(Senses::class);
        $senses->shouldReceive('getValue')
            ->andReturn($value);
        $senses->shouldReceive('__toString')
            ->andReturn((string)$value);

        return $senses;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Roll2d6DrdPlus
     */
    private function createRoll2d6DrdPlus($value)
    {
        $roll2d6DrdPlus = $this->mockery(Roll2d6DrdPlus::class);
        $roll2d6DrdPlus->shouldReceive('getValue')
            ->andReturn($value);
        $roll2d6DrdPlus->shouldReceive('__toString')
            ->andReturn((string)$value);

        return $roll2d6DrdPlus;
    }

}