<?php
declare(strict_types=1);

namespace DrdPlus\HuntingAndFishing;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;

/**
 * See PPH page 133 left column, @link https://pph.drdplus.info/#hod_na_kvalitu_pokrmu
 * @method Roll2d6DrdPlus getRoll()
 */
class CatchProcessingQuality extends RollOnQuality
{
    /**
     * @param Knack $knack
     * @param Cooking $cooking
     * @param Roll2d6DrdPlus $roll2d6DrdPlus
     */
    public function __construct(Knack $knack, Cooking $cooking, Roll2d6DrdPlus $roll2d6DrdPlus)
    {
        parent::__construct($knack->getValue() + $cooking->getBonus(), $roll2d6DrdPlus);
    }
}