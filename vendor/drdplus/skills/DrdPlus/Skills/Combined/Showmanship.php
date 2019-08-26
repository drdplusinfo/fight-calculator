<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\Skills\Combined\RollsOnQuality\ShowmanshipGameQuality;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#herectvi
 */
class Showmanship extends CombinedSkill implements WithBonus
{
    public const SHOWMANSHIP = CombinedSkillCode::SHOWMANSHIP;

    public function getName(): string
    {
        return self::SHOWMANSHIP;
    }

    public function getBonus(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }

    /**
     * @link https://pph.drdplus.info/#vypocet_kvality_hry_pri_herectvi
     * @param Charisma $charisma
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @return ShowmanshipGameQuality
     */
    public function getShowmanshipGameQuality(Charisma $charisma, Roll2d6DrdPlus $roll2D6DrdPlus): ShowmanshipGameQuality
    {
        return new ShowmanshipGameQuality($charisma, $this, $roll2D6DrdPlus);
    }
}