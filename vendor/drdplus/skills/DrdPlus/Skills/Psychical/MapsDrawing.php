<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Psychical\RollsOn\MapQuality;
use DrdPlus\Skills\Psychical\RollsOn\RollOnMapUsage;
use DrdPlus\Skills\WithBonus;

/**
 * @link https://pph.drdplus.info/#kresleni_map
 */
class MapsDrawing extends PsychicalSkill implements WithBonus
{
    public const MAPS_DRAWING = PsychicalSkillCode::MAPS_DRAWING;

    public function getName(): string
    {
        return self::MAPS_DRAWING;
    }

    public function getBonus(): int
    {
        return $this->getCurrentSkillRank()->getValue() * 2;
    }

    public function getCreatedMapQuality(Knack $knack, Roll2d6DrdPlus $roll2D6DrdPlus): MapQuality
    {
        return new MapQuality($knack, $this, $roll2D6DrdPlus);
    }

    public function getRollOnMapUsage(Intelligence $intelligence, Roll2d6DrdPlus $roll2D6DrdPlus): RollOnMapUsage
    {
        return new RollOnMapUsage($intelligence, $this, $roll2D6DrdPlus);
    }

    public function getBonusToNavigation(MapQuality $usedMapQuality, RollOnMapUsage $rollOnThatMapUsage): int
    {
        $usefulQuality = min($usedMapQuality->getValue(), $rollOnThatMapUsage->getValue());

        return SumAndRound::round($usefulQuality / 6);
    }

}