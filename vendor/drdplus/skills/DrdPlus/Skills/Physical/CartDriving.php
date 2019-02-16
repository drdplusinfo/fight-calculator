<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;

/**
 * @link https://pph.drdplus.info/#rizeni_vozu
 */
class CartDriving extends PhysicalSkill
{
    public const CART_DRIVING = PhysicalSkillCode::CART_DRIVING;

    public function getName(): string
    {
        return self::CART_DRIVING;
    }

    public function getMalusToMovementSpeed(): int
    {
        return -3 + $this->getCurrentSkillRank()->getValue();
    }
}