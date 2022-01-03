<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rolls\Roll1d6;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Property;

class ShortRollOnIntelligence extends ShortRollOnProperty
{
    /**
     * @param Intelligence $intelligence
     * @param Roll1d6 $roll1d6
     */
    public function __construct(Intelligence $intelligence, Roll1d6 $roll1d6)
    {
        parent::__construct($intelligence, $roll1d6);
    }

    /**
     * @return Intelligence|Property
     */
    public function getIntelligence(): Intelligence
    {
        return $this->getProperty();
    }
}