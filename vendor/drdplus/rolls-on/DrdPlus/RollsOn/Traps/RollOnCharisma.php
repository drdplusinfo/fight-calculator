<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use DrdPlus\BaseProperties\Charisma;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;

class RollOnCharisma extends RollOnQuality
{
    /**
     * @var Charisma
     */
    private $charisma;

    public function __construct(Charisma $charisma, Roll2d6DrdPlus $roll2d6DrdPlus)
    {
        $this->charisma = $charisma;
        parent::__construct($charisma->getValue(), $roll2d6DrdPlus);
    }

    public function getCharisma(): Charisma
    {
        return $this->charisma;
    }
}