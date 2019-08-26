<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#letectvi
 */
class Flying extends PhysicalSkill implements WithBonus
{
    public const FLYING = PhysicalSkillCode::FLYING;

    public function getName(): string
    {
        return self::FLYING;
    }

    public function getBonus(): int
    {
        return $this->getCurrentSkillRank()->getValue() * 2;
    }

    public function getMalusToFight(): int
    {
        return -9 + 3 * $this->getCurrentSkillRank()->getValue();
    }

}