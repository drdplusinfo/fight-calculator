<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Skills\WithBonusToCharisma;

/**
 * @link https://pph.drdplus.info/#svadeni
 */
class Seduction extends CombinedSkill implements WithBonusToCharisma
{
    public const SEDUCTION = CombinedSkillCode::SEDUCTION;

    public function getName(): string
    {
        return self::SEDUCTION;
    }

    public function getBonusToCharisma(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

}