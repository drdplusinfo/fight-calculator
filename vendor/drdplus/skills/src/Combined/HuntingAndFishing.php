<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\HuntingAndFishing\WithBonusFromHuntingAndFishingSkill;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#lov_a_rybolov
 */
class HuntingAndFishing extends CombinedSkill implements WithBonus, WithBonusFromHuntingAndFishingSkill
{
    public const HUNTING_AND_FISHING = CombinedSkillCode::HUNTING_AND_FISHING;

    public function getName(): string
    {
        return self::HUNTING_AND_FISHING;
    }

    public function getBonus(): int
    {
        return 2 * $this->getCurrentSkillRank()->getValue();
    }

    public function getBonusFromSkill(): int
    {
        return $this->getBonus();
    }

}