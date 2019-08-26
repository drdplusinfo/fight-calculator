<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Skills\Physical\Flying;

class FlyingTest extends WithBonusFromPhysicalTest
{
    /**
     * @param int $skillRankValue
     * @return int
     */
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return $skillRankValue * 2;
    }

    /**
     * @test
     */
    public function I_can_get_malus_from_flight()
    {
        $flying = new Flying($this->createProfessionLevel());
        self::assertSame(-9, $flying->getMalusToFight());
        $flying->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-6, $flying->getMalusToFight());
        $flying->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-3, $flying->getMalusToFight());
        $flying->increaseSkillRank($this->createSkillPoint());
        self::assertSame(0, $flying->getMalusToFight());
    }

}