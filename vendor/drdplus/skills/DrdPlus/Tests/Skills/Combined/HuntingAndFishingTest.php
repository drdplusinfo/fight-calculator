<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\HuntingAndFishing\WithBonusFromHuntingAndFishingSkill;
use DrdPlus\Skills\Combined\HuntingAndFishing;

class HuntingAndFishingTest extends WithBonusFromCombinedTest
{
    /**
     * @test
     */
    public function I_can_use_it_as_hunting_and_fishing_bonus()
    {
        $huntingAndFishing = new HuntingAndFishing($this->createProfessionLevel());
        self::assertInstanceOf(WithBonusFromHuntingAndFishingSkill::class, $huntingAndFishing);
        self::assertSame($huntingAndFishing->getBonus(), $huntingAndFishing->getBonusFromSkill());

        $huntingAndFishing->increaseSkillRank($this->createSkillPoint());
        self::assertSame($huntingAndFishing->getBonus(), $huntingAndFishing->getBonusFromSkill());

        $huntingAndFishing->increaseSkillRank($this->createSkillPoint());
        self::assertSame($huntingAndFishing->getBonus(), $huntingAndFishing->getBonusFromSkill());

        $huntingAndFishing->increaseSkillRank($this->createSkillPoint());
        self::assertSame($huntingAndFishing->getBonus(), $huntingAndFishing->getBonusFromSkill());
    }
}