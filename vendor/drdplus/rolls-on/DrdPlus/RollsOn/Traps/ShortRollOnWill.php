<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rolls\Roll1d6;
use DrdPlus\BaseProperties\Will;
use DrdPlus\BaseProperties\Property;

class ShortRollOnWill extends ShortRollOnProperty
{
    /**
     * @param Will $will
     * @param Roll1d6 $roll1d6
     */
    public function __construct(Will $will, Roll1d6 $roll1d6)
    {
        parent::__construct($will, $roll1d6);
    }

    /**
     * @return Will|Property
     */
    public function getWill(): Will
    {
        return $this->getProperty();
    }
}