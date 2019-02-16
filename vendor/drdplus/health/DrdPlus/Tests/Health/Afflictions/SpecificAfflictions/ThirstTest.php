<?php
namespace DrdPlus\Tests\Health\Afflictions\SpecificAfflictions;

use DrdPlus\Health\Afflictions\Affliction;
use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\SpecificAfflictions\Thirst;
use DrdPlus\Health\Health;
use DrdPlus\Tests\Health\Afflictions\AfflictionTest;

class ThirstTest extends AfflictionTest
{
    /**
     * @test
     */
    public function It_is_linked_with_health_immediately()
    {
        $afflictions = [];
        $thirst = Thirst::createIt($health = $this->createHealth($afflictions), AfflictionSize::getIt(123));
        self::assertSame($afflictions, [$thirst]);
        $healthProperty = new \ReflectionProperty(Affliction::class, 'health');
        $healthProperty->setAccessible(true);
        self::assertSame($health, $healthProperty->getValue($thirst));
    }

    /**
     * @param array $afflictionsContainer
     * @return \Mockery\MockInterface|Health
     */
    private function createHealth(array &$afflictionsContainer)
    {
        $health = $this->mockery(Health::class);
        $health->shouldReceive('addAffliction')
            ->atLeast()->once()
            ->with($this->type(Thirst::class))
            ->andReturnUsing(function ($thirst) use (&$afflictionsContainer, $health) {
                $afflictionsContainer[] = $thirst;

                return $health;
            });

        return $health;
    }

    /**
     * @test
     */
    public function I_can_use_it()
    {
        $thirst = Thirst::createIt(new Health(), AfflictionSize::getIt(567));
        self::assertInstanceOf(Thirst::class, $thirst);
    }

    /**
     * @return Thirst
     */
    protected function getSut(): Thirst
    {
        return Thirst::createIt(new Health(), AfflictionSize::getIt(46));
    }

    /**
     * @test
     */
    public function I_can_get_heal_malus()
    {
        $thirst = Thirst::createIt(new Health(), AfflictionSize::getIt(567));
        self::assertSame(0, $thirst->getHealMalus());
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_activities()
    {
        $thirst = Thirst::createIt(new Health(), AfflictionSize::getIt(567));
        self::assertSame(0, $thirst->getMalusToActivities());
    }

    /**
     * @test
     */
    public function I_can_get_strength_malus()
    {
        $thirst = Thirst::createIt(new Health(), AfflictionSize::getIt(567));
        self::assertSame(-567, $thirst->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_agility_malus()
    {
        $thirst = Thirst::createIt(new Health(), AfflictionSize::getIt(567));
        self::assertSame(-567, $thirst->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_knack_malus()
    {
        $thirst = Thirst::createIt(new Health(), AfflictionSize::getIt(567));
        self::assertSame(-567, $thirst->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_will_malus()
    {
        $thirst = Thirst::createIt(new Health(), AfflictionSize::getIt(567));
        self::assertSame(-567, $thirst->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_intelligence_malus()
    {
        $thirst = Thirst::createIt(new Health(), AfflictionSize::getIt(567));
        self::assertSame(-567, $thirst->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_charisma_malus()
    {
        $thirst = Thirst::createIt(new Health(), AfflictionSize::getIt(567));
        self::assertSame(-567, $thirst->getStrengthMalus());
    }

}