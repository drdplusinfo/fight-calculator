<?php declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Skills\WithBonusToCharisma;

/**
 * @link https://pph.drdplus.info/#etiketa_podsveti
 */
class EtiquetteOfGangland extends PsychicalSkill implements WithBonusToCharisma
{
    public const ETIQUETTE_OF_GANGLAND = PsychicalSkillCode::ETIQUETTE_OF_GANGLAND;

    public function getName(): string
    {
        return self::ETIQUETTE_OF_GANGLAND;
    }

    public function getBonusToCharisma(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }
}