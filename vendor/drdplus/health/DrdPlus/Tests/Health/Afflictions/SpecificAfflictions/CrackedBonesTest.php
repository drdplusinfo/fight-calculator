<?php
namespace DrdPlus\Tests\Health\Afflictions\SpecificAfflictions;

use DrdPlus\Health\Afflictions\Effects\CrackedBonesEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\EarthPertinence;
use DrdPlus\Health\Afflictions\SpecificAfflictions\CrackedBones;
use DrdPlus\Tests\Health\Afflictions\AfflictionByWoundTest;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Health\Afflictions\AfflictionDangerousness;
use DrdPlus\Health\Afflictions\AfflictionDomain;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\AfflictionProperty;
use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\AfflictionSource;
use DrdPlus\Health\Afflictions\AfflictionVirulence;

class CrackedBonesTest extends AfflictionByWoundTest
{
    /**
     * @return CrackedBones
     */
    protected function getSut(): CrackedBones
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(15);
        $this->addSizeCalculation($wound, $woundBoundary, 3);

        return CrackedBones::createIt($wound, $woundBoundary);
    }

    /**
     * @test
     */
    public function I_can_use_it()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(15);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 3);
        $crackedBones = CrackedBones::createIt($wound, $woundBoundary);

        self::assertSame($wound, $crackedBones->getSeriousWound());

        self::assertInstanceOf(AfflictionDomain::class, $crackedBones->getDomain());
        self::assertSame(AfflictionDomain::PHYSICAL, $crackedBones->getDomain()->getValue());

        self::assertInstanceOf(AfflictionVirulence::class, $crackedBones->getVirulence());
        self::assertSame(AfflictionVirulence::DAY, $crackedBones->getVirulence()->getValue());

        self::assertInstanceOf(AfflictionSource::class, $crackedBones->getSource());
        self::assertSame(AfflictionSource::PASSIVE, $crackedBones->getSource()->getValue());

        self::assertInstanceOf(AfflictionProperty::class, $crackedBones->getProperty());
        self::assertSame(PropertyCode::TOUGHNESS, $crackedBones->getProperty()->getValue());

        self::assertInstanceOf(AfflictionDangerousness::class, $crackedBones->getDangerousness());
        self::assertSame(15, $crackedBones->getDangerousness()->getValue());

        self::assertInstanceOf(AfflictionSize::class, $crackedBones->getAfflictionSize());
        self::assertSame($filledHalfOfRows * 2, $crackedBones->getAfflictionSize()->getValue());

        self::assertInstanceOf(EarthPertinence::class, $crackedBones->getElementalPertinence());
        self::assertTrue($crackedBones->getElementalPertinence()->isMinus());

        self::assertInstanceOf(CrackedBonesEffect::class, $crackedBones->getAfflictionEffect());

        self::assertInstanceOf(\DateInterval::class, $crackedBones->getOutbreakPeriod());
        self::assertSame('0y0m0d0h0i0s', $crackedBones->getOutbreakPeriod()->format('%yy%mm%dd%hh%ii%ss'));

        self::assertInstanceOf(AfflictionName::class, $crackedBones->getName());
        self::assertSame('cracked_bones', $crackedBones->getName()->getValue());
    }

    /**
     * @test
     */
    public function I_can_get_heal_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(15);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $crackedBones = CrackedBones::createIt($wound, $woundBoundary);
        self::assertSame(-246, $crackedBones->getHealMalus());
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_activities()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $crackedBones = CrackedBones::createIt($wound, $woundBoundary);
        self::assertSame(0, $crackedBones->getMalusToActivities());
    }

    /**
     * @test
     */
    public function I_can_get_strength_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $crackedBones = CrackedBones::createIt($wound, $woundBoundary);
        self::assertSame(0, $crackedBones->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_agility_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $crackedBones = CrackedBones::createIt($wound, $woundBoundary);
        self::assertSame(0, $crackedBones->getAgilityMalus());
    }

    /**
     * @test
     */
    public function I_can_get_knack_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, $filledHalfOfRows = 123);
        $crackedBones = CrackedBones::createIt($wound, $woundBoundary);
        self::assertSame(0, $crackedBones->getKnackMalus());
    }

}