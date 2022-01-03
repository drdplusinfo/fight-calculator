<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Skills\WithBonusToCharisma;

/**
 * @link https://pph.drdplus.info/#vychovatelstvi
 */
class Pedagogy extends CombinedSkill implements WithBonusToCharisma
{
    public const PEDAGOGY = CombinedSkillCode::PEDAGOGY;

    public function getName(): string
    {
        return self::PEDAGOGY;
    }

    public function getBonusToCharisma(): int
    {
        return 2 * $this->getCurrentSkillRank()->getValue();
    }

}