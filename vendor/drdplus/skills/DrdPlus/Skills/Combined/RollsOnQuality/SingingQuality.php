<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Combined\RollsOnQuality;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Skills\Combined\Singing;

/**
 * See PPH page 155 right column, @link https://pph.drdplus.info/#vypocet_kvality_zpevu
 * @method Roll2d6DrdPlus getRoll()
 */
class SingingQuality extends RollOnQuality
{
    /**
     * @param Knack $knack
     * @param Singing $singing
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     */
    public function __construct(
        Knack $knack,
        Singing $singing,
        Roll2d6DrdPlus $roll2D6DrdPlus
    )
    {
        parent::__construct($knack->getValue() + $singing->getBonus(), $roll2D6DrdPlus);
    }

}