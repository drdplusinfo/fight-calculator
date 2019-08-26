<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#splh_a_lezeni
 */
class ClimbingAndHillwalking extends PhysicalSkill implements WithBonus
{
    public const CLIMBING_AND_HILLWALKING = PhysicalSkillCode::CLIMBING_AND_HILLWALKING;

    public function getName(): string
    {
        return self::CLIMBING_AND_HILLWALKING;
    }

    public function getBonus(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }
}