<?php declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Skills\WithBonusToIntelligence;

/**
 * @link https://pph.drdplus.info/#bajeslovi
 */
class Mythology extends PsychicalSkill implements WithBonusToIntelligence
{
    public const MYTHOLOGY = PsychicalSkillCode::MYTHOLOGY;

    public function getName(): string
    {
        return self::MYTHOLOGY;
    }

    public function getBonusToIntelligence(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }
}