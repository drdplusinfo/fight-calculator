<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Combined;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\HuntingAndFishing\CatchProcessingQuality;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#vareni
 */
class Cooking extends CombinedSkill implements WithBonus, \DrdPlus\HuntingAndFishing\Cooking
{
    public const COOKING = CombinedSkillCode::COOKING;

    public function getName(): string
    {
        return self::COOKING;
    }

    /**
     * @link https://pph.drdplus.info/#hod_na_zpracovani_ulovku
     * @return int
     */
    public function getBonus(): int
    {
        return 2 * $this->getCurrentSkillRank()->getValue();
    }

    /**
     * @link https://pph.drdplus.info/#hod_na_zpracovani_ulovku
     * @param Knack $knack
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @return CatchProcessingQuality
     */
    public function createCatchProcessingQuality(Knack $knack, Roll2d6DrdPlus $roll2D6DrdPlus): CatchProcessingQuality
    {
        return new CatchProcessingQuality($knack, $this, $roll2D6DrdPlus);
    }

}