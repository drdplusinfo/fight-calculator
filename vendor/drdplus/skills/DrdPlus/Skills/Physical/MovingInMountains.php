<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonusToMovementSpeed;

/**
 * @link https://pph.drdplus.info/#pohyb_v_horach
 */
class MovingInMountains extends PhysicalSkill implements WithBonusToMovementSpeed
{
    public const MOVING_IN_MOUNTAINS = PhysicalSkillCode::MOVING_IN_MOUNTAINS;

    public function getName(): string
    {
        return self::MOVING_IN_MOUNTAINS;
    }

    public function getBonusToMovementSpeed(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

}