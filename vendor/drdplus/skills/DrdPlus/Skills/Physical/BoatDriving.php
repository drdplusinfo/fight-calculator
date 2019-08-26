<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonusToMovementSpeed;

/**
 * @link https://pph.drdplus.info/#ovladani_lodky
 */
class BoatDriving extends PhysicalSkill implements WithBonusToMovementSpeed
{
    public const BOAT_DRIVING = PhysicalSkillCode::BOAT_DRIVING;

    public function getName(): string
    {
        return self::BOAT_DRIVING;
    }

    public function getBonusToMovementSpeed(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

}