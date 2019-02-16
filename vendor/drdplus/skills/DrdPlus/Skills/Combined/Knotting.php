<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Skills\WithBonusToKnack;

/**
 * @link https://pph.drdplus.info/#uzlovani
 */
class Knotting extends CombinedSkill implements WithBonusToKnack
{
    public const KNOTTING = CombinedSkillCode::KNOTTING;

    public function getName(): string
    {
        return self::KNOTTING;
    }

    public function getBonusToKnack(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

}