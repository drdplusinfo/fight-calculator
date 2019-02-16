<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Combined\RollsOnQuality\PaintingQuality;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#malovani
 */
class Painting extends CombinedSkill implements WithBonus
{
    public const PAINTING = CombinedSkillCode::PAINTING;

    public function getName(): string
    {
        return self::PAINTING;
    }

    public function getBonus(): int
    {
        return 3 * $this->getCurrentSkillRank()->getValue();
    }

    /**
     * @link https://pph.drdplus.info/#vypocet_kvality_obrazu
     * @param Knack $knack
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @return PaintingQuality
     */
    public function getPaintingQuality(Knack $knack, Roll2d6DrdPlus $roll2D6DrdPlus): PaintingQuality
    {
        return new PaintingQuality($knack, $this, $roll2D6DrdPlus);
    }
}