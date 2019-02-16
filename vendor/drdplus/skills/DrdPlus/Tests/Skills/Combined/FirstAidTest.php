<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Skills\Combined\FirstAid;

class FirstAidTest extends CombinedSkillTest
{
    /**
     * @test
     */
    public function I_can_get_minimal_wounds_left_after_first_aid_heal()
    {
        $firstAid = new FirstAid($this->createProfessionFirstLevel());
        self::assertSame(5, $firstAid->getMinimalWoundsLeftAfterFirstAidHeal());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(3, $firstAid->getMinimalWoundsLeftAfterFirstAidHeal());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(2, $firstAid->getMinimalWoundsLeftAfterFirstAidHeal());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(1, $firstAid->getMinimalWoundsLeftAfterFirstAidHeal());
    }

    /**
     * @test
     */
    public function I_can_get_healing_power_to_basic_wounds()
    {
        $firstAid = new FirstAid($this->createProfessionFirstLevel());
        self::assertSame(-20, $firstAid->getHealingPowerToBasicWounds());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-6, $firstAid->getHealingPowerToBasicWounds());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-4, $firstAid->getHealingPowerToBasicWounds());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-2, $firstAid->getHealingPowerToBasicWounds());
    }

    /**
     * @test
     */
    public function I_can_get_bleeding_lowering_value()
    {
        $firstAid = new FirstAid($this->createProfessionFirstLevel());
        self::assertSame(0, $firstAid->getBleedingLoweringValue());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(0, $firstAid->getBleedingLoweringValue());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-1, $firstAid->getBleedingLoweringValue());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-2, $firstAid->getBleedingLoweringValue());
    }

    /**
     * @test
     */
    public function I_can_get_poison_lowering_value()
    {
        $firstAid = new FirstAid($this->createProfessionFirstLevel());
        self::assertSame(0, $firstAid->getPoisonLoweringValue());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(0, $firstAid->getPoisonLoweringValue());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(0, $firstAid->getPoisonLoweringValue());
        $firstAid->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-2, $firstAid->getPoisonLoweringValue());
    }
}