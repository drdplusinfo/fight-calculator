<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Combined\RollsOnQuality;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Skills\Combined\Painting;

/**
 * See PPH page 153 right column, @link https://pph.drdplus.info/#vypocet_kvality_obrazu
 * @method Roll2d6DrdPlus getRoll()
 */
class PaintingQuality extends RollOnQuality
{
    /**
     * @param Knack $knack
     * @param Painting $painting
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     */
    public function __construct(Knack $knack, Painting $painting, Roll2d6DrdPlus $roll2D6DrdPlus)
    {
        parent::__construct($knack->getValue() + $painting->getBonus(), $roll2D6DrdPlus);
    }
}