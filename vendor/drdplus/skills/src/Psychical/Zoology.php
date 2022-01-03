<?php declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Skills\WithBonusToIntelligence;

/**
 * @link https://pph.drdplus.info/#zoologie
 */
class Zoology extends PsychicalSkill implements WithBonusToIntelligence
{
    public const ZOOLOGY = PsychicalSkillCode::ZOOLOGY;

    public function getName(): string
    {
        return self::ZOOLOGY;
    }

    public function getBonusToIntelligence(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }

    public function getBonusToAttackNumberAgainstFreeWillAnimal(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

    public function getBonusToCoverAgainstFreeWillAnimal(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

    public function getBonusToBaseOfWoundsAgainstFreeWillAnimal(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }
}