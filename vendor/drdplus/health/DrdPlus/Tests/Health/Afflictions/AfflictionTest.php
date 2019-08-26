<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions;

use DrdPlus\Health\Afflictions\Affliction;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\AfflictionProperty;
use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\AfflictionVirulence;
use DrdPlus\Health\Afflictions\Effects\AfflictionEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\ElementalPertinence;
use Granam\Tests\Tools\TestWithMockery;

abstract class AfflictionTest extends TestWithMockery
{

    /**
     * @test
     */
    abstract public function It_is_linked_with_health_immediately();

    /**
     * @test
     */
    abstract public function I_can_use_it();

    /**
     * @return \Mockery\MockInterface|AfflictionVirulence
     */
    protected function createAfflictionVirulence()
    {
        return $this->mockery(AfflictionVirulence::class);
    }

    /**
     * @return \Mockery\MockInterface|AfflictionProperty
     */
    protected function createAfflictionProperty()
    {
        return $this->mockery(AfflictionProperty::class);
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|AfflictionSize
     */
    protected function createAfflictionSize($value = null)
    {
        $afflictionSize = $this->mockery(AfflictionSize::class);
        if ($value !== null) {
            $afflictionSize->shouldReceive('getValue')
                ->andReturn($value);
        }

        return $afflictionSize;
    }

    /**
     * @return \Mockery\MockInterface|ElementalPertinence
     */
    protected function createElementalPertinence()
    {
        return $this->mockery(ElementalPertinence::class);
    }

    /**
     * @return \Mockery\MockInterface|AfflictionEffect
     */
    protected function createAfflictionEffect()
    {
        return $this->mockery(AfflictionEffect::class);
    }

    /**
     * @return \Mockery\MockInterface|\DateInterval
     */
    protected function createOutbreakPeriod()
    {
        return $this->mockery(\DateInterval::class);
    }

    /**
     * @return \Mockery\MockInterface|AfflictionName
     */
    protected function createAfflictionName()
    {
        return $this->mockery(AfflictionName::class);
    }

    /**
     * @test
     */
    public function I_get_all_maluses_zero_or_lesser()
    {
        $affliction = $this->getSut();
        self::assertLessThanOrEqual(0, $affliction->getStrengthMalus());
        self::assertLessThanOrEqual(0, $affliction->getAgilityMalus());
        self::assertLessThanOrEqual(0, $affliction->getKnackMalus());
        self::assertLessThanOrEqual(0, $affliction->getWillMalus());
        self::assertLessThanOrEqual(0, $affliction->getIntelligenceMalus());
        self::assertLessThanOrEqual(0, $affliction->getCharismaMalus());
    }

    /**
     * @return Affliction
     */
    abstract protected function getSut();

    /**
     * @test
     */
    abstract public function I_can_get_heal_malus();

    /**
     * @test
     */
    abstract public function I_can_get_malus_to_activities();

    /**
     * @test
     */
    abstract public function I_can_get_strength_malus();

    /**
     * @test
     */
    abstract public function I_can_get_agility_malus();

    /**
     * @test
     */
    abstract public function I_can_get_knack_malus();

    /**
     * @test
     */
    abstract public function I_can_get_will_malus();

    /**
     * @test
     */
    abstract public function I_can_get_intelligence_malus();

    /**
     * @test
     */
    abstract public function I_can_get_charisma_malus();

}