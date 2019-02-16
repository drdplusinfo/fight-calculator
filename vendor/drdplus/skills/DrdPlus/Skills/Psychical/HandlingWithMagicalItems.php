<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Skills\WithBonusToIntelligence;

/**
 * @link https://pph.drdplus.info/#zachazeni_s_magickymi_predmety
 */
class HandlingWithMagicalItems extends PsychicalSkill implements WithBonusToIntelligence
{
    public const HANDLING_WITH_MAGICAL_ITEMS = PsychicalSkillCode::HANDLING_WITH_MAGICAL_ITEMS;

    public function getName(): string
    {
        return self::HANDLING_WITH_MAGICAL_ITEMS;
    }

    public function getBonusToIntelligence(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }

    public function automaticallyRecognizesSameMagicalItemInvestigatedBefore(): bool
    {
        return $this->getCurrentSkillRank()->getValue() >= 2;
    }

    public function automaticallyRecognizesCategoryOfMagicalItemInvestigatedBefore(): bool
    {
        return $this->getCurrentSkillRank()->getValue() >= 3;
    }

}