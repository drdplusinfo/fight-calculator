<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Skills\Combined\CombinedSkill;
use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\Physical\PhysicalSkill;
use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\Psychical\PsychicalSkill;
use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Skills\WithBonusToSenses;

/**
 * @method static assertTrue($current, $message)
 * @method static assertSame($expected, $current)
 * @method static getSutClass
 * @method ProfessionLevel createProfessionLevel
 * @method PsychicalSkillPoint|PhysicalSkillPoint|CombinedSkillPoint createSkillPoint
 */
trait WithBonusToSensesTrait
{
    /**
     * @test
     */
    public function It_has_expected_with_bonus_to_sense_interface()
    {
        self::assertTrue(
            is_a(self::getSutClass(), WithBonusToSenses::class, true),
            self::getSutClass() . ' should implement ' . WithBonusToSenses::class
        );
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_senses()
    {
        $sutClass = self::getSutClass();
        /** @var WithBonusToSenses|PsychicalSkill|PhysicalSkill|CombinedSkill $sut */
        $sut = new $sutClass($this->createProfessionLevel());

        self::assertSame(0, $sut->getCurrentSkillRank()->getValue());
        self::assertSame(0, $sut->getBonusToSenses());

        $sut->increaseSkillRank($this->createSkillPoint());
        self::assertSame(1, $sut->getCurrentSkillRank()->getValue());
        self::assertSame($this->getExpectedBonusToSenses(1), $sut->getBonusToSenses());

        $sut->increaseSkillRank($this->createSkillPoint());
        self::assertSame(2, $sut->getCurrentSkillRank()->getValue());
        self::assertSame($this->getExpectedBonusToSenses(2), $sut->getBonusToSenses());

        $sut->increaseSkillRank($this->createSkillPoint());
        self::assertSame(3, $sut->getCurrentSkillRank()->getValue());
        self::assertSame($this->getExpectedBonusToSenses(3), $sut->getBonusToSenses());
    }

    abstract protected function getExpectedBonusToSenses(int $currentSkillRankValue): int;
}