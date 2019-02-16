<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Skills\WithBonusToIntelligence;
use DrdPlus\Skills\WithBonusToSenses;

/**
 * @link https://pph.drdplus.info/#technologie
 */
class Technology extends PsychicalSkill implements WithBonusToIntelligence, WithBonusToSenses
{
    public const TECHNOLOGY = PsychicalSkillCode::TECHNOLOGY;

    public function getName(): string
    {
        return self::TECHNOLOGY;
    }

    public function getBonusToIntelligence(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }

    /**
     * ONLY for searching hidden mechanisms
     *
     * @return int
     */
    public function getBonusToSenses(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

}