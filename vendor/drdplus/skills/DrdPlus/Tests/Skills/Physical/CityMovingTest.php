<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Skills\Physical\CityMoving;
use DrdPlus\Tests\Skills\WithBonusToMovementSpeedTest;

class CityMovingTest extends WithBonusToMovementSpeedTest
{
    use CreatePhysicalSkillPointTrait;

    /**
     * @test
     */
    public function I_can_get_bonus_to_speed_and_intelligence_or_senses()
    {
        $swimming = new CityMoving($this->createProfessionLevel());

        self::assertSame(0, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(0, $swimming->getBonusToMovementSpeed());
        self::assertSame(0, $swimming->getBonusToIntelligenceOrSenses());

        $swimming->increaseSkillRank($this->createSkillPoint());
        self::assertSame(1, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(1, $swimming->getBonusToMovementSpeed());
        self::assertSame(2, $swimming->getBonusToIntelligenceOrSenses());

        $swimming->increaseSkillRank($this->createSkillPoint());
        self::assertSame(2, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(2, $swimming->getBonusToMovementSpeed());
        self::assertSame(4, $swimming->getBonusToIntelligenceOrSenses());

        $swimming->increaseSkillRank($this->createSkillPoint());
        self::assertSame(3, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(3, $swimming->getBonusToMovementSpeed());
        self::assertSame(6, $swimming->getBonusToIntelligenceOrSenses());
    }
}