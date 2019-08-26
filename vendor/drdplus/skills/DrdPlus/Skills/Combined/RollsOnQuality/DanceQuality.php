<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined\RollsOnQuality;

use Granam\DiceRolls\Roll;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Skills\Combined\Dancing;

/**
 * See PPH page 154 right column, @link https://pph.drdplus.info/#tanec
 * @method Roll2d6DrdPlus getRoll()
 */
class DanceQuality extends RollOnQuality
{
    /**
     * @link https://pph.drdplus.info/#vypocet_kvality_tance
     * @param Agility $agility
     * @param Dancing $dancing
     * @param Roll $roll
     */
    public function __construct(Agility $agility, Dancing $dancing, Roll $roll)
    {
        parent::__construct($agility->getValue() + $dancing->getBonus(), $roll);
    }

}