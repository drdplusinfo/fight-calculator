<?php declare(strict_types=1);

namespace DrdPlus\Skills\Psychical\RollsOn;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Skills\Psychical\MapsDrawing;

/**
 * See PPH page 150 right column, @link https://pph.drdplus.info/#vypocet_kvality_mapy
 * @method Roll2d6DrdPlus getRoll()
 */
class MapQuality extends RollOnQuality
{
    public function __construct(Knack $knack, MapsDrawing $mapsDrawing, Roll2d6DrdPlus $roll2D6DrdPlus)
    {
        parent::__construct($knack->getValue() + $mapsDrawing->getBonus(), $roll2D6DrdPlus);
    }

}