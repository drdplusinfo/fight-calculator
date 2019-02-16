<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use DrdPlus\BaseProperties\Property;
use Granam\DiceRolls\Templates\Rolls\Roll1d6;
use DrdPlus\BaseProperties\Agility;

class ShortRollOnAgility extends ShortRollOnProperty
{
    public function __construct(Agility $agility, Roll1d6 $roll1d6)
    {
        parent::__construct($agility, $roll1d6);
    }

    /**
     * @return Agility|Property
     */
    public function getAgility(): Agility
    {
        return $this->getProperty();
    }
}