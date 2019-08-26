<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonusToMovementSpeed;

/**
 * @link https://pph.drdplus.info/#rychly_pochod
 */
class FastMarsh extends PhysicalSkill implements WithBonusToMovementSpeed
{
    public const FAST_MARSH = PhysicalSkillCode::FAST_MARSH;

    public function getName(): string
    {
        return self::FAST_MARSH;
    }

    public function getBonusToMovementSpeed(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

}