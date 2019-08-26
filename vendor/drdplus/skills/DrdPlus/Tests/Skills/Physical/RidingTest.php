<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Codes\Transport\MovementTypeCode;
use DrdPlus\Skills\Physical\Riding;
use DrdPlus\Tests\Skills\WithBonusTest;

class RidingTest extends WithBonusTest
{
    use CreatePhysicalSkillPointTrait;

    /**
     * @param int $skillRankValue
     * @return int
     * @throws \LogicException
     */
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        switch ($skillRankValue) {
            case 1 :
                return 4;
            case 2 :
                return 6;
            case 3 :
                return 8;
            default :
                throw new \LogicException('Unexpected skill rank value ' . $skillRankValue);
        }
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_fight_and_attack_and_defense_number()
    {
        $riding = new Riding($this->createProfessionLevel());
        self::assertSame(-6, $riding->getMalusToFightAttackAndDefenseNumber());
        $riding->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-4, $riding->getMalusToFightAttackAndDefenseNumber());
        $riding->increaseSkillRank($this->createSkillPoint());
        self::assertSame(-2, $riding->getMalusToFightAttackAndDefenseNumber());
        $riding->increaseSkillRank($this->createSkillPoint());
        self::assertSame(0, $riding->getMalusToFightAttackAndDefenseNumber());
    }

    /**
     * @test
     */
    public function I_can_get_horse_move_fatigue_equivalent()
    {
        $riding = new Riding($this->createProfessionLevel());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WALK), $riding->getGaitWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::RUSH), $riding->getTrotWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::RUN), $riding->getCanterWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::SPRINT), $riding->getGallopWearyLike());
        $riding->increaseSkillRank($this->createSkillPoint());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WAITING), $riding->getGaitWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WALK), $riding->getTrotWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::RUSH), $riding->getCanterWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::RUN), $riding->getGallopWearyLike());
        $riding->increaseSkillRank($this->createSkillPoint());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WAITING), $riding->getGaitWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WAITING), $riding->getTrotWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WALK), $riding->getCanterWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::RUSH), $riding->getGallopWearyLike());
        $riding->increaseSkillRank($this->createSkillPoint());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WAITING), $riding->getGaitWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WAITING), $riding->getTrotWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WAITING), $riding->getCanterWearyLike());
        self::assertSame(MovementTypeCode::getIt(MovementTypeCode::WAITING), $riding->getGallopWearyLike());
    }
}