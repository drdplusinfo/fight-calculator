<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonusToMovementSpeed;

/**
 * @link https://pph.drdplus.info/#pohyb_ve_meste
 */
class CityMoving extends PhysicalSkill implements WithBonusToMovementSpeed
{
    public const CITY_MOVING = PhysicalSkillCode::CITY_MOVING;

    public function getName(): string
    {
        return self::CITY_MOVING;
    }

    public function getBonusToMovementSpeed(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

    public function getBonusToIntelligenceOrSenses(): int
    {
        return 2 * $this->getCurrentSkillRank()->getValue();
    }
}