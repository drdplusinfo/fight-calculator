<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Combined\RollsOnQuality;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Skills\Combined\Handwork;

/**
 * See PPH page 131 left column, @link https://pph.drdplus.info/#hod_na_kvalitu_rucni_prace
 * @method Roll2d6DrdPlus getRoll()
 */
class HandworkQuality extends RollOnQuality
{
    /**
     * @param Knack $knack
     * @param Handwork $handwork
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     */
    public function __construct(Knack $knack, Handwork $handwork, Roll2d6DrdPlus $roll2D6DrdPlus)
    {
        parent::__construct($knack->getValue() + $handwork->getBonusToKnack(), $roll2D6DrdPlus);
    }
}