<?php declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Skills\WithBonusToCharisma;

/**
 * @link https://pph.drdplus.info/#spolecenska_etiketa
 */
class SocialEtiquette extends PsychicalSkill implements WithBonusToCharisma
{
    public const SOCIAL_ETIQUETTE = PsychicalSkillCode::SOCIAL_ETIQUETTE;

    public function getName(): string
    {
        return self::SOCIAL_ETIQUETTE;
    }

    public function getBonusToCharisma(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }
}