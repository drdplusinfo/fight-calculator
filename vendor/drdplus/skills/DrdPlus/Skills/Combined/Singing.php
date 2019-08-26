<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Combined\RollsOnQuality\SingingQuality;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#zpev
 */
class Singing extends CombinedSkill implements WithBonus
{
    public const SINGING = CombinedSkillCode::SINGING;

    public function getName(): string
    {
        return self::SINGING;
    }

    public function getBonus(): int
    {
        return $this->getCurrentSkillRank()->getValue() * 3;
    }

    public function canImitateBirdSong(): bool
    {
        return $this->getCurrentSkillRank()->getValue() >= 3;
    }

    public function createSingingQuality(Knack $knack, Roll2d6DrdPlus $roll2D6DrdPlus): SingingQuality
    {
        return new SingingQuality($knack, $this, $roll2D6DrdPlus);
    }
}