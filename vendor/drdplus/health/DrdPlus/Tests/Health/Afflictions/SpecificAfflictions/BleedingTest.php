<?php
namespace DrdPlus\Tests\Health\Afflictions\SpecificAfflictions;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Health\Afflictions\AfflictionDangerousness;
use DrdPlus\Health\Afflictions\AfflictionDomain;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\AfflictionProperty;
use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\AfflictionSource;
use DrdPlus\Health\Afflictions\AfflictionVirulence;
use DrdPlus\Health\Afflictions\Effects\BleedingEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\WaterPertinence;
use DrdPlus\Health\Afflictions\SpecificAfflictions\Bleeding;
use DrdPlus\Tests\Health\Afflictions\AfflictionByWoundTest;

class BleedingTest extends AfflictionByWoundTest
{
    /**
     * @return Bleeding
     */
    protected function getSut(): Bleeding
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);

        return Bleeding::createIt($wound, $woundBoundary);
    }

    /**
     * @test
     */
    public function I_can_use_it()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $bleeding = Bleeding::createIt($wound, $woundBoundary);

        self::assertSame($wound, $bleeding->getSeriousWound());

        self::assertInstanceOf(AfflictionDomain::class, $bleeding->getDomain());
        self::assertSame('physical', $bleeding->getDomain()->getValue());

        self::assertInstanceOf(AfflictionVirulence::class, $bleeding->getVirulence());
        self::assertSame(AfflictionVirulence::ROUND, $bleeding->getVirulence()->getValue());

        self::assertInstanceOf(AfflictionSource::class, $bleeding->getSource());
        self::assertSame(AfflictionSource::ACTIVE, $bleeding->getSource()->getValue());

        self::assertInstanceOf(AfflictionProperty::class, $bleeding->getProperty());
        self::assertSame(PropertyCode::TOUGHNESS, $bleeding->getProperty()->getValue());

        self::assertInstanceOf(AfflictionDangerousness::class, $bleeding->getDangerousness());
        self::assertSame(15, $bleeding->getDangerousness()->getValue());

        self::assertInstanceOf(AfflictionSize::class, $bleeding->getAfflictionSize());
        self::assertSame($filledHalfOfRows - 1, $bleeding->getAfflictionSize()->getValue());

        self::assertInstanceOf(WaterPertinence::class, $bleeding->getElementalPertinence());
        self::assertTrue($bleeding->getElementalPertinence()->isMinus());

        self::assertInstanceOf(BleedingEffect::class, $bleeding->getAfflictionEffect());

        self::assertInstanceOf(\DateInterval::class, $bleeding->getOutbreakPeriod());
        self::assertSame('0y0m0d0h0i0s', $bleeding->getOutbreakPeriod()->format('%yy%mm%dd%hh%ii%ss'));

        self::assertInstanceOf(AfflictionName::class, $bleeding->getName());
        self::assertSame('bleeding', $bleeding->getName()->getValue());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Afflictions\SpecificAfflictions\Exceptions\BleedingCanNotExistsDueToTooLowWound
     */
    public function I_can_not_create_it_from_too_low_wound()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(5);
        $this->addSizeCalculation($wound, $woundBoundary, 0);
        Bleeding::createIt($wound, $woundBoundary);
    }

    /**
     * @test
     */
    public function I_can_get_heal_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $bleeding = Bleeding::createIt($wound, $woundBoundary);
        self::assertSame(0, $bleeding->getHealMalus());
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_activities()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $bleeding = Bleeding::createIt($wound, $woundBoundary);
        self::assertSame(0, $bleeding->getMalusToActivities());
    }

    /**
     * @test
     */
    public function I_can_get_strength_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $bleeding = Bleeding::createIt($wound, $woundBoundary);
        self::assertSame(0, $bleeding->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_agility_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $bleeding = Bleeding::createIt($wound, $woundBoundary);
        self::assertSame(0, $bleeding->getAgilityMalus());
    }

    /**
     * @test
     */
    public function I_can_get_knack_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $bleeding = Bleeding::createIt($wound, $woundBoundary);
        self::assertSame(0, $bleeding->getKnackMalus());
    }

}