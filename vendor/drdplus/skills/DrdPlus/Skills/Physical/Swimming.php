<?php declare(strict_types=1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonusToMovementSpeed;

/**
 * @link https://pph.drdplus.info/#plavani
 */
class Swimming extends PhysicalSkill implements WithBonusToMovementSpeed
{
    public const SWIMMING = PhysicalSkillCode::SWIMMING;

    public function getName(): string
    {
        return self::SWIMMING;
    }

    public function getBonusToSwimming(): int
    {
        $currentSkillRankValue = $this->getCurrentSkillRank()->getValue();
        if ($currentSkillRankValue === 0) {
            return 0;
        }

        return $currentSkillRankValue * 2 + 2;
    }

    public function getBonusToMovementSpeed(): int
    {
        $currentSkillRankValue = $this->getCurrentSkillRank()->getValue();
        if ($currentSkillRankValue === 0) {
            return 0;
        }

        return $currentSkillRankValue + 1;
    }
}