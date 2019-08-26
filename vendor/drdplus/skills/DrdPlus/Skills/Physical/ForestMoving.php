<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonusToMovementSpeed;

/**
 * @link https://pph.drdplus.info/#pohyb_v_lese
 */
class ForestMoving extends PhysicalSkill implements WithBonusToMovementSpeed
{
    public const FOREST_MOVING = PhysicalSkillCode::FOREST_MOVING;

    public function getName(): string
    {
        return self::FOREST_MOVING;
    }

    public function getBonusToMovementSpeed(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

}