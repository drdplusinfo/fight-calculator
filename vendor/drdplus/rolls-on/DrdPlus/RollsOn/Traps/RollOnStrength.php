<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;

class RollOnStrength extends RollOnQuality
{
    /**
     * @var Strength
     */
    private $strength;

    public function __construct(Strength $strength, Roll2d6DrdPlus $roll2d6DrdPlus)
    {
        $this->strength = $strength;
        parent::__construct($strength->getValue(), $roll2d6DrdPlus);
    }

    public function getStrength(): Strength
    {
        return $this->strength;
    }
}