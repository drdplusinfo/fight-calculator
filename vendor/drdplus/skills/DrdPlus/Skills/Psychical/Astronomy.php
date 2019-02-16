<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Skills\WithBonusToIntelligence;

/**
 * @link https://pph.drdplus.info/#astronomie
 */
class Astronomy extends PsychicalSkill implements WithBonusToIntelligence
{
    public const ASTRONOMY = PsychicalSkillCode::ASTRONOMY;

    public function getName(): string
    {
        return self::ASTRONOMY;
    }

    public function getBonusToIntelligence(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }

    public function getBonusToOrientation(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }
}