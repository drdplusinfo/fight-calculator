<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use DrdPlus\BaseProperties\Agility;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;

class RollOnAgility extends RollOnQuality
{
    /**
     * @var Agility
     */
    private $agility;

    public function __construct(Agility $agility, Roll2d6DrdPlus $roll2d6DrdPlus)
    {
        $this->agility = $agility;
        parent::__construct($agility->getValue(), $roll2d6DrdPlus);
    }

    public function getAgility(): Agility
    {
        return $this->agility;
    }
}