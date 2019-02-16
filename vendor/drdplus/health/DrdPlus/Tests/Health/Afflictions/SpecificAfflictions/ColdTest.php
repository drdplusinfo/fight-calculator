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
use DrdPlus\Health\Afflictions\Effects\ColdEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\WaterPertinence;
use DrdPlus\Health\Afflictions\SpecificAfflictions\Cold;
use DrdPlus\Tests\Health\Afflictions\AfflictionByWoundTest;

class ColdTest extends AfflictionByWoundTest
{
    /**
     * @return Cold
     */
    protected function getSut(): Cold
    {
        return Cold::createIt($this->createWound());
    }

    /**
     * @test
     */
    public function I_can_use_it()
    {
        $cold = Cold::createIt($wound = $this->createWound());

        self::assertSame($wound, $cold->getSeriousWound());

        self::assertInstanceOf(AfflictionDomain::class, $cold->getDomain());
        self::assertSame(AfflictionDomain::PHYSICAL, $cold->getDomain()->getValue());

        self::assertInstanceOf(AfflictionVirulence::class, $cold->getVirulence());
        self::assertSame(AfflictionVirulence::DAY, $cold->getVirulence()->getValue());

        self::assertInstanceOf(AfflictionSource::class, $cold->getSource());
        self::assertSame(AfflictionSource::ACTIVE, $cold->getSource()->getValue());

        self::assertInstanceOf(AfflictionProperty::class, $cold->getProperty());
        self::assertSame(PropertyCode::TOUGHNESS, $cold->getProperty()->getValue());

        self::assertInstanceOf(AfflictionDangerousness::class, $cold->getDangerousness());
        self::assertSame(7, $cold->getDangerousness()->getValue());

        self::assertInstanceOf(AfflictionSize::class, $cold->getAfflictionSize());
        self::assertSame(4, $cold->getAfflictionSize()->getValue());

        self::assertInstanceOf(WaterPertinence::class, $cold->getElementalPertinence());
        self::assertTrue($cold->getElementalPertinence()->isPlus());

        self::assertInstanceOf(ColdEffect::class, $cold->getAfflictionEffect());

        self::assertInstanceOf(\DateInterval::class, $cold->getOutbreakPeriod());
        self::assertSame('0y0m1d0h0i0s', $cold->getOutbreakPeriod()->format('%yy%mm%dd%hh%ii%ss'));

        self::assertInstanceOf(AfflictionName::class, $cold->getName());
        self::assertSame('cold', $cold->getName()->getValue());
    }

    /**
     * @test
     */
    public function I_can_get_heal_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $cold = Cold::createIt($wound);
        self::assertSame(0, $cold->getHealMalus());
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_activities()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $cold = Cold::createIt($wound);
        self::assertSame(0, $cold->getMalusToActivities());
    }

    /**
     * @test
     */
    public function I_can_get_strength_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $cold = Cold::createIt($wound);
        self::assertSame(-1, $cold->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_agility_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $cold = Cold::createIt($wound);
        self::assertSame(-1, $cold->getAgilityMalus());
    }

    /**
     * @test
     */
    public function I_can_get_knack_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $cold = Cold::createIt($wound);
        self::assertSame(-1, $cold->getKnackMalus());
    }
}