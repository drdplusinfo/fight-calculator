<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rolls\Roll1d6;
use DrdPlus\BaseProperties\Property;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;

abstract class ShortRollOnProperty extends RollOnQuality
{
    /**
     * @var Property
     */
    private $property;

    /**
     * @param Property $property
     * @param Roll1d6 $roll1d6
     */
    public function __construct(Property $property, Roll1d6 $roll1d6)
    {
        $this->property = $property;
        parent::__construct($property->getValue(), $roll1d6);
    }

    protected function getProperty(): Property
    {
        return $this->property;
    }
}