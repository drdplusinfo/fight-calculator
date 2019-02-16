<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\Skills\Combined\RollsOnQuality\DanceQuality;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#tanec
 */
class Dancing extends CombinedSkill implements WithBonus
{
    public const DANCING = CombinedSkillCode::DANCING;

    public function getName(): string
    {
        return self::DANCING;
    }

    public function getBonus(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }

    /**
     * @param Agility $agility
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @return DanceQuality
     */
    public function createDanceQuality(Agility $agility, Roll2d6DrdPlus $roll2D6DrdPlus): DanceQuality
    {
        return new DanceQuality($agility, $this, $roll2D6DrdPlus);
    }

}