<?php declare(strict_types=1);

namespace DrdPlus\Skills;

use DrdPlus\Calculations\SumAndRound;

/**
 * @method SkillRank getCurrentSkillRank
 */
trait FightWithWeaponsMissingSkillMalusesTrait
{
    public function getMalusToFightNumber(): int
    {
        return $this->getCurrentSkillRank()->getValue() - 3;
    }

    public function getMalusToAttackNumber(): int
    {
        return $this->getCurrentSkillRank()->getValue() - 3;
    }

    public function getMalusToCover(): int
    {
        /*
         * (0 + 2) / 2↑ - 3 = 1 - 3 = -2
         * (1 + 2) / 2↑ - 3 = 2 - 3 = -1
         * (2 + 2) / 2↑ - 3 = 2 - 3 = -1
         * (3 + 2) / 2↑ - 3 = 3 - 3 = 0
        */
        return SumAndRound::ceiledHalf($this->getCurrentSkillRank()->getValue() + 2) - 3;
    }

    public function getMalusToBaseOfWounds(): int
    {
        /*
         * 0 / 2↓ - 3 = 0 - 1 = -1
         * 1 / 2↓ - 3 = 0 - 1 = -1
         * 2 / 2↓ - 3 = 1 - 1 = 0
         * 3 / 2↓ - 3 = 1 - 1 = 0
        */
        return SumAndRound::flooredHalf($this->getCurrentSkillRank()->getValue()) - 1;
    }
}