<?php declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Skills\WithBonusToIntelligence;

/**
 * @link https://pph.drdplus.info/#znalost_mesta
 */
class KnowledgeOfACity extends PsychicalSkill implements WithBonusToIntelligence
{
    public const KNOWLEDGE_OF_A_CITY = PsychicalSkillCode::KNOWLEDGE_OF_A_CITY;

    public function getName(): string
    {
        return self::KNOWLEDGE_OF_A_CITY;
    }

    public function getBonusToIntelligence(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }

}