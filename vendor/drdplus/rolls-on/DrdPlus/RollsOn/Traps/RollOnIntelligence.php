<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;

class RollOnIntelligence extends RollOnQuality
{
    /**
     * @var Intelligence
     */
    private $intelligence;

    public function __construct(Intelligence $intelligence, Roll2d6DrdPlus $roll2d6DrdPlus)
    {
        $this->intelligence = $intelligence;
        parent::__construct($intelligence->getValue(), $roll2d6DrdPlus);
    }

    public function getIntelligence(): Intelligence
    {
        return $this->intelligence;
    }
}