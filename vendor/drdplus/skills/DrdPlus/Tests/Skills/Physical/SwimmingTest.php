<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Skills\Physical\Swimming;
use DrdPlus\Tests\Skills\WithBonusToMovementSpeedTest;

class SwimmingTest extends WithBonusToMovementSpeedTest
{
    /**
     * @test
     */
    public function I_can_get_bonus_to_swimming_and_speed()
    {
        $swimming = new Swimming($this->createProfessionLevel());

        self::assertSame(0, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(0, $swimming->getBonusToSwimming());
        self::assertSame(0, $swimming->getBonusToMovementSpeed());

        $swimming->increaseSkillRank($this->createSkillPoint());
        self::assertSame(1, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(4, $swimming->getBonusToSwimming());
        self::assertSame(2, $swimming->getBonusToMovementSpeed());

        $swimming->increaseSkillRank($this->createSkillPoint());
        self::assertSame(2, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(6, $swimming->getBonusToSwimming());
        self::assertSame(3, $swimming->getBonusToMovementSpeed());

        $swimming->increaseSkillRank($this->createSkillPoint());
        self::assertSame(3, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(8, $swimming->getBonusToSwimming());
        self::assertSame(4, $swimming->getBonusToMovementSpeed());
    }

    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return $skillRankValue === 0
            ? 0
            : $skillRankValue + 1;
    }

}