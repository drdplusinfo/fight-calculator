<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Psychical;

use DrdPlus\Skills\Psychical\Astronomy;

class AstronomyTest extends WithBonusToIntelligenceFromPsychicalTest
{
    /**
     * @test
     */
    public function I_can_get_bonus_to_orientation()
    {
        $astronomy = new Astronomy($this->createProfessionLevel());

        self::assertSame(0, $astronomy->getCurrentSkillRank()->getValue());
        self::assertSame(0, $astronomy->getBonusToOrientation());

        $astronomy->increaseSkillRank($this->createSkillPoint());
        self::assertSame(1, $astronomy->getCurrentSkillRank()->getValue());
        self::assertSame(1, $astronomy->getBonusToOrientation());

        $astronomy->increaseSkillRank($this->createSkillPoint());
        self::assertSame(2, $astronomy->getCurrentSkillRank()->getValue());
        self::assertSame(2, $astronomy->getBonusToOrientation());

        $astronomy->increaseSkillRank($this->createSkillPoint());
        self::assertSame(3, $astronomy->getCurrentSkillRank()->getValue());
        self::assertSame(3, $astronomy->getBonusToOrientation());
    }
}