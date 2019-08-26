<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonusToMovementSpeed;

/**
 * @link https://pph.drdplus.info/#namornictvi
 */
class Sailing extends PhysicalSkill implements WithBonusToMovementSpeed
{
    public const SAILING = PhysicalSkillCode::SAILING;

    public function getName(): string
    {
        return self::SAILING;
    }

    public function getBonusToMovementSpeed(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

}