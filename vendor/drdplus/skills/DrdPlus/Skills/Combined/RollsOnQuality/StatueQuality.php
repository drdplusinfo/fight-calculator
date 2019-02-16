<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Combined\RollsOnQuality;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Skills\Combined\Singing;
use DrdPlus\Skills\Combined\Statuary;

/**
 * See PPH page 154 left column, @link https://pph.drdplus.info/#socharstvi
 * @method Roll2d6DrdPlus getRoll()
 */
class StatueQuality extends RollOnQuality
{
    /**
     * @param Knack $knack
     * @param Statuary $statuary
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     */
    public function __construct(
        Knack $knack,
        Statuary $statuary,
        Roll2d6DrdPlus $roll2D6DrdPlus
    )
    {
        parent::__construct($knack->getValue() + $statuary->getBonus(), $roll2D6DrdPlus);
    }

}