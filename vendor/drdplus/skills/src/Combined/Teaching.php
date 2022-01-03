<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#vyucovani
 */
class Teaching extends CombinedSkill implements WithBonus
{
    public const TEACHING = CombinedSkillCode::TEACHING;

    public function getName(): string
    {
        return self::TEACHING;
    }

    public function getBonus(): int
    {
        return $this->getCurrentSkillRank()->getValue() * 2;
    }

}