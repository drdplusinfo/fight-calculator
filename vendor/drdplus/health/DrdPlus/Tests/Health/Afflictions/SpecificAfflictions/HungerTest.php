<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions\SpecificAfflictions;

use DrdPlus\Health\Afflictions\AfflictionDangerousness;
use DrdPlus\Health\Afflictions\AfflictionDomain;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\AfflictionProperty;
use DrdPlus\Health\Afflictions\AfflictionSource;
use DrdPlus\Health\Afflictions\AfflictionVirulence;
use DrdPlus\Health\Afflictions\Effects\HungerEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\EarthPertinence;
use DrdPlus\Health\Afflictions\SpecificAfflictions\Hunger;
use DrdPlus\Health\Health;
use DrdPlus\Tests\Health\Afflictions\AfflictionTest;

class HungerTest extends AfflictionTest
{
    /**
     * @return Hunger
     */
    protected function getSut(): Hunger
    {
        return Hunger::createIt(new Health(), $this->createAfflictionSize(4));
    }

    /**
     * @test
     */
    public function It_is_linked_with_health_immediately()
    {

        $health = new Health();
        $hunger = Hunger::createIt($health, $this->createAfflictionSize());
        self::assertSame([$hunger], $health->getAfflictions());
    }

    /**
     * @test
     */
    public function I_can_use_it()
    {
        $health = new Health();
        $hunger = Hunger::createIt($health, $this->createAfflictionSize(7));
        self::assertInstanceOf(Hunger::class, $hunger);
        $anotherHunger = Hunger::createIt($health, $this->createAfflictionSize());
        self::assertNotSame($hunger, $anotherHunger);
        self::assertSame(HungerEffect::getIt(), $hunger->getAfflictionEffect());
        self::assertInstanceOf(AfflictionDangerousness::class, $hunger->getDangerousness()); // irrelevant
        self::assertSame(AfflictionDomain::getPhysicalDomain(), $hunger->getDomain());
        self::assertSame(EarthPertinence::getMinus(), $hunger->getElementalPertinence());
        self::assertSame(AfflictionVirulence::getDayVirulence(), $hunger->getVirulence());
        self::assertSame(AfflictionSource::getPassiveSource(), $hunger->getSource());
        self::assertSame(AfflictionProperty::getIt(AfflictionProperty::ENDURANCE), $hunger->getProperty());
        self::assertEquals(new \DateInterval('P1D'), $hunger->getOutbreakPeriod());
        self::assertEquals(AfflictionName::getIt('hunger'), $hunger->getName());
    }

    /**
     * @test
     */
    public function I_can_get_heal_malus()
    {
        $hunger = Hunger::createIt(new Health(), $this->createAfflictionSize(546));
        self::assertSame(0, $hunger->getHealMalus());
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_activities()
    {
        $hunger = Hunger::createIt(new Health(), $this->createAfflictionSize(987));
        self::assertSame(0, $hunger->getMalusToActivities());
    }

    /**
     * @test
     */
    public function I_can_get_strength_malus()
    {
        $hunger = Hunger::createIt(new Health(), $this->createAfflictionSize(3));
        self::assertInstanceOf(Hunger::class, $hunger);
        self::assertSame(-2, $hunger->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_agility_malus()
    {
        $health = new Health();
        $hunger = Hunger::createIt($health, $this->createAfflictionSize(6));
        self::assertSame(-3, $hunger->getAgilityMalus());
    }

    /**
     * @test
     */
    public function I_can_get_knack_malus()
    {
        $hunger = Hunger::createIt(new Health(), $this->createAfflictionSize(21));
        self::assertSame(-11, $hunger->getKnackMalus());
    }

    public function I_can_get_will_malus()
    {
        $hunger = Hunger::createIt(new Health(), $this->createAfflictionSize(123541));
        self::assertSame(0, $hunger->getKnackMalus());
    }

    public function I_can_get_intelligence_malus()
    {
        $hunger = Hunger::createIt(new Health(), $this->createAfflictionSize(65178));
        self::assertSame(0, $hunger->getKnackMalus());
    }

    public function I_can_get_charisma_malus()
    {
        $hunger = Hunger::createIt(new Health(), $this->createAfflictionSize(782));
        self::assertSame(0, $hunger->getKnackMalus());
    }

}