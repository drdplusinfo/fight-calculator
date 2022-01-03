<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions\SpecificAfflictions;

use DrdPlus\Health\Afflictions\AfflictionDangerousness;
use DrdPlus\Health\Afflictions\AfflictionDomain;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\AfflictionProperty;
use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\AfflictionSource;
use DrdPlus\Health\Afflictions\AfflictionVirulence;
use DrdPlus\Health\Afflictions\Effects\SeveredArmEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\EarthPertinence;
use DrdPlus\Health\Afflictions\SpecificAfflictions\SeveredArm;
use DrdPlus\Tests\Health\Afflictions\AfflictionByWoundTest;

class SeveredArmTest extends AfflictionByWoundTest
{
    /**
     * @return SeveredArm
     */
    protected function getSut(): SeveredArm
    {
        return SeveredArm::createIt($this->createWound());
    }

    /**
     * @test
     */
    public function I_can_use_it()
    {
        $severedArm = SeveredArm::createIt($wound = $this->createWound());

        self::assertSame($wound, $severedArm->getSeriousWound());

        self::assertInstanceOf(AfflictionDomain::class, $severedArm->getDomain());
        self::assertSame('physical', $severedArm->getDomain()->getValue());

        self::assertInstanceOf(AfflictionVirulence::class, $severedArm->getVirulence());
        self::assertSame(AfflictionVirulence::DAY, $severedArm->getVirulence()->getValue());

        self::assertInstanceOf(AfflictionSource::class, $severedArm->getSource());
        self::assertSame(AfflictionSource::FULL_DEFORMATION, $severedArm->getSource()->getValue());

        self::assertInstanceOf(AfflictionProperty::class, $severedArm->getProperty());

        self::assertInstanceOf(AfflictionDangerousness::class, $severedArm->getDangerousness());

        self::assertInstanceOf(AfflictionSize::class, $severedArm->getAfflictionSize());
        self::assertSame(6 /* by default*/, $severedArm->getAfflictionSize()->getValue());

        self::assertInstanceOf(EarthPertinence::class, $severedArm->getElementalPertinence());
        self::assertTrue($severedArm->getElementalPertinence()->isMinus());

        self::assertInstanceOf(SeveredArmEffect::class, $severedArm->getAfflictionEffect());

        self::assertInstanceOf(\DateInterval::class, $severedArm->getOutbreakPeriod());
        self::assertSame('0y0m0d0h0i0s', $severedArm->getOutbreakPeriod()->format('%yy%mm%dd%hh%ii%ss'));

        self::assertInstanceOf(AfflictionName::class, $severedArm->getName());
        self::assertSame('completely_severed_arm', $severedArm->getName()->getValue());
    }

    /**
     * @test
     */
    public function I_can_create_partially_severed_arm()
    {
        $severedArm = SeveredArm::createIt($this->createWound(), $sizeValue = 1);

        self::assertInstanceOf(AfflictionDomain::class, $severedArm->getDomain());
        self::assertSame('physical', $severedArm->getDomain()->getValue());

        self::assertInstanceOf(AfflictionVirulence::class, $severedArm->getVirulence());
        self::assertSame(AfflictionVirulence::DAY, $severedArm->getVirulence()->getValue());

        self::assertInstanceOf(AfflictionSource::class, $severedArm->getSource());
        self::assertSame(AfflictionSource::FULL_DEFORMATION, $severedArm->getSource()->getValue());

        self::assertInstanceOf(AfflictionProperty::class, $severedArm->getProperty());

        self::assertInstanceOf(AfflictionDangerousness::class, $severedArm->getDangerousness());

        self::assertInstanceOf(AfflictionSize::class, $severedArm->getAfflictionSize());
        self::assertSame($sizeValue, $severedArm->getAfflictionSize()->getValue());

        self::assertInstanceOf(EarthPertinence::class, $severedArm->getElementalPertinence());
        self::assertTrue($severedArm->getElementalPertinence()->isMinus());

        self::assertInstanceOf(SeveredArmEffect::class, $severedArm->getAfflictionEffect());

        self::assertInstanceOf(\DateInterval::class, $severedArm->getOutbreakPeriod());
        self::assertSame('0y0m0d0h0i0s', $severedArm->getOutbreakPeriod()->format('%yy%mm%dd%hh%ii%ss'));

        self::assertInstanceOf(AfflictionName::class, $severedArm->getName());
        self::assertSame('severed_arm', $severedArm->getName()->getValue());
    }

    /**
     * @test
     */
    public function I_can_not_create_more_than_completely_severed_arm()
    {
        $this->expectException(\DrdPlus\Health\Afflictions\SpecificAfflictions\Exceptions\SeveredArmAfflictionSizeExceeded::class);
        SeveredArm::createIt($this->createWound(), 7);
    }

    /**
     * @test
     */
    public function I_can_not_create_severed_arm_with_negative_value()
    {
        $this->expectException(\DrdPlus\Health\Afflictions\Exceptions\AfflictionSizeCanNotBeNegative::class);
        try {
            SeveredArm::createIt($this->createWound(), 0);
        } catch (\Exception $e) {
            self::fail('No exception expected so far: ' . $e->getTraceAsString());
        }

        SeveredArm::createIt($this->createWound(), -1);
    }

    /**
     * @test
     */
    public function I_can_get_heal_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $severedArm = SeveredArm::createIt($wound);
        self::assertSame(0, $severedArm->getHealMalus());
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_activities()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $severedArm = SeveredArm::createIt($wound);
        self::assertSame(0, $severedArm->getMalusToActivities());
    }

    /**
     * @test
     */
    public function I_can_get_strength_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $severedArm = SeveredArm::createIt($wound, 5);
        self::assertSame(-5, $severedArm->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_agility_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $severedArm = SeveredArm::createIt($wound);
        self::assertSame(0, $severedArm->getAgilityMalus());
    }

    /**
     * @test
     */
    public function I_can_get_knack_malus()
    {
        $wound = $this->createWound();
        $woundBoundary = $this->createWoundBoundary(20);
        $this->addSizeCalculation($wound, $woundBoundary, 123);
        $severedArm = SeveredArm::createIt($wound, 4);
        self::assertSame(-8, $severedArm->getKnackMalus());
    }
}