<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use DrdPlus\BaseProperties\Will;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;

class RollOnWill extends RollOnQuality
{
    /**
     * @var Will
     */
    private $will;

    public function __construct(Will $will, Roll2d6DrdPlus $roll2d6DrdPlus)
    {
        $this->will = $will;
        parent::__construct($will->getValue(), $roll2d6DrdPlus);
    }

    public function getWill(): Will
    {
        return $this->will;
    }
}