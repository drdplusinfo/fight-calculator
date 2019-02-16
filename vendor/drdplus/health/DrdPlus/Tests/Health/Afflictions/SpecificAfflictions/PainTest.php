<?php
namespace DrdPlus\Tests\Health\Afflictions\SpecificAfflictions;

use DrdPlus\Health\Afflictions\Effects\PainEffect;
use DrdPlus\Health\Afflictions\SpecificAfflictions\Pain;
use DrdPlus\Tests\Health\Afflictions\AfflictionByWoundTest;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Health\Afflictions\AfflictionDangerousness;
use DrdPlus\Health\Afflictions\AfflictionDomain;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\AfflictionProperty;
use DrdPlus\Health\Afflictions\AfflictionSource;

class PainTest extends AfflictionByWoundTest
{
    /**
     * @return Pain
     */
    protected function getSut(): Pain
    {
        return Pain::createIt(
            $this->createWound(),
            $this->createAfflictionVirulence(),
            $this->createAfflictionSize(123),
            $this->createElementalPertinence()
        );
    }

    /**
     * @test
     */
    public function I_can_use_it()
    {
        $seriousWound = $this->createWound();
        $someTerriblePain = Pain::createIt(
            $seriousWound,
            $virulence = $this->createAfflictionVirulence(),
            $size = $this->createAfflictionSize($painValue = 123),
            $elementalPertinence = $this->createElementalPertinence()
        );

        self::assertSame($seriousWound, $someTerriblePain->getSeriousWound());

        self::assertInstanceOf(AfflictionDomain::class, $someTerriblePain->getDomain());
        self::assertSame(AfflictionDomain::PHYSICAL, $someTerriblePain->getDomain()->getValue());

        self::assertSame($virulence, $someTerriblePain->getVirulence());

        self::assertInstanceOf(AfflictionSource::class, $someTerriblePain->getSource());
        self::assertSame(AfflictionSource::EXTERNAL, $someTerriblePain->getSource()->getValue());

        self::assertInstanceOf(AfflictionProperty::class, $someTerriblePain->getProperty());
        self::assertSame(PropertyCode::WILL, $someTerriblePain->getProperty()->getValue());

        self::assertInstanceOf(AfflictionDangerousness::class, $someTerriblePain->getDangerousness());
        self::assertSame($painValue + 10, $someTerriblePain->getDangerousness()->getValue());

        self::assertSame($size, $someTerriblePain->getAfflictionSize());

        self::assertSame($elementalPertinence, $someTerriblePain->getElementalPertinence());

        self::assertInstanceOf(PainEffect::class, $someTerriblePain->getAfflictionEffect());

        self::assertInstanceOf(\DateInterval::class, $someTerriblePain->getOutbreakPeriod());
        self::assertSame('0y0m0d0h0i0s', $someTerriblePain->getOutbreakPeriod()->format('%yy%mm%dd%hh%ii%ss'));

        self::assertInstanceOf(AfflictionName::class, $someTerriblePain->getName());
        self::assertSame('pain', $someTerriblePain->getName()->getValue());
    }

    /**
     * @test
     */
    public function I_can_get_heal_malus()
    {
        $seriousWound = $this->createWound();
        $someTerriblePain = Pain::createIt(
            $seriousWound,
            $virulence = $this->createAfflictionVirulence(),
            $size = $this->createAfflictionSize($painValue = 123),
            $elementalPertinence = $this->createElementalPertinence()
        );
        self::assertSame(0, $someTerriblePain->getHealMalus());
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_activities()
    {
        $seriousWound = $this->createWound();
        $someTerriblePain = Pain::createIt(
            $seriousWound,
            $virulence = $this->createAfflictionVirulence(),
            $size = $this->createAfflictionSize($painValue = 123),
            $elementalPertinence = $this->createElementalPertinence()
        );
        self::assertSame(-$painValue, $someTerriblePain->getMalusToActivities());
    }

    /**
     * @test
     */
    public function I_can_get_strength_malus()
    {
        $seriousWound = $this->createWound();
        $someTerriblePain = Pain::createIt(
            $seriousWound,
            $virulence = $this->createAfflictionVirulence(),
            $size = $this->createAfflictionSize($painValue = 123),
            $elementalPertinence = $this->createElementalPertinence()
        );
        self::assertSame(0, $someTerriblePain->getStrengthMalus());
    }

    /**
     * @test
     */
    public function I_can_get_agility_malus()
    {
        $seriousWound = $this->createWound();
        $someTerriblePain = Pain::createIt(
            $seriousWound,
            $virulence = $this->createAfflictionVirulence(),
            $size = $this->createAfflictionSize($painValue = 123),
            $elementalPertinence = $this->createElementalPertinence()
        );
        self::assertSame(0, $someTerriblePain->getAgilityMalus());
    }

    /**
     * @test
     */
    public function I_can_get_knack_malus()
    {
        $seriousWound = $this->createWound();
        $someTerriblePain = Pain::createIt(
            $seriousWound,
            $virulence = $this->createAfflictionVirulence(),
            $size = $this->createAfflictionSize($painValue = 123),
            $elementalPertinence = $this->createElementalPertinence()
        );
        self::assertSame(0, $someTerriblePain->getKnackMalus());
    }
}