<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#socharstvi
 */
class Statuary extends CombinedSkill implements WithBonus
{
    public const STATUARY = CombinedSkillCode::STATUARY;

    public function getName(): string
    {
        return self::STATUARY;
    }

    public function getBonus(): int
    {
        return $this->getCurrentSkillRank()->getValue() * 3;
    }

}