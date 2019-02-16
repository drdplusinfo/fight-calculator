<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Properties\Derived\Senses;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;

class RollOnSenses extends RollOnQuality
{
    /**
     * @var Senses
     */
    private $senses;
    /**
     * @var BonusFromUsedRemarkableSense
     */
    private $bonusFromUsedRemarkableSense;

    public function __construct(
        Senses $senses,
        Roll2d6DrdPlus $roll2d6DrdPlus,
        BonusFromUsedRemarkableSense $bonusFromUsedRemarkableSense
    )
    {
        $this->senses = $senses;
        $this->bonusFromUsedRemarkableSense = $bonusFromUsedRemarkableSense;
        parent::__construct($senses->getValue() + $bonusFromUsedRemarkableSense->getValue(), $roll2d6DrdPlus);
    }

    public function getValueWithoutBonusFromUsedRemarkableSense(): int
    {
        return $this->getValue() - $this->getBonusFromUsedRemarkableSense()->getValue();
    }

    public function getSenses(): Senses
    {
        return $this->senses;
    }

    public function getBonusFromUsedRemarkableSense(): BonusFromUsedRemarkableSense
    {
        return $this->bonusFromUsedRemarkableSense;
    }

}