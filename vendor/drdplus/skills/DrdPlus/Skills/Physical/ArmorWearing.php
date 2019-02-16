<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#noseni_zbroje
 */
class ArmorWearing extends PhysicalSkill implements WithBonus
{
    public const ARMOR_WEARING = PhysicalSkillCode::ARMOR_WEARING;

    public function getName(): string
    {
        return self::ARMOR_WEARING;
    }

    public function getBonus(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

}