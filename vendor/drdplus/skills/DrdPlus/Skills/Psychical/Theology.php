<?php declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Skills\WithBonusToIntelligence;

/**
 * @link https://pph.drdplus.info/#teologie
 */
class Theology extends PsychicalSkill implements WithBonusToIntelligence
{
    public const THEOLOGY = PsychicalSkillCode::THEOLOGY;

    public function getName(): string
    {
        return self::THEOLOGY;
    }

    public function getBonusToIntelligence(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }
}