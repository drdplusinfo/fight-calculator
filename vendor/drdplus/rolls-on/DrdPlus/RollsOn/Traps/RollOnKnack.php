<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;

class RollOnKnack extends RollOnQuality
{
    /**
     * @var Knack
     */
    private $knack;

    public function __construct(Knack $knack, Roll2d6DrdPlus $roll2d6DrdPlus)
    {
        $this->knack = $knack;
        parent::__construct($knack->getValue(), $roll2d6DrdPlus);
    }

    public function getKnack(): Knack
    {
        return $this->knack;
    }
}