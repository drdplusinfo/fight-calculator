<?php
declare(strict_types=1);

namespace DrdPlus\HuntingAndFishing;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Calculations\SumAndRound;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Tables\Measurements\Amount\Amount;
use DrdPlus\Tables\Measurements\Time\Time;

/**
 * See PPH page 132, @link https://pph.drdplus.jaroslavtyc.com/#lov_a_rybolov
 */
class CatchQuality extends RollOnQuality
{
    public const STANDARD_HUNTING_TIME_IN_BONUS = 57; // 2 hours

    /**
     * @param HuntPrerequisite $huntPrerequisite
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @param Amount $requiredAmountOfMeals
     * @param Time $huntingTime
     * @throws \DrdPlus\HuntingAndFishing\Exceptions\HuntingTimeIsTooShort
     */
    public function __construct(
        HuntPrerequisite $huntPrerequisite,
        Roll2d6DrdPlus $roll2D6DrdPlus,
        Amount $requiredAmountOfMeals,
        Time $huntingTime
    )
    {
        parent::__construct(
            $huntPrerequisite->getValue()
            // workaround to impossible round on whole result (which would round 9 - 10.5 - 0 + 13 to 12, but without roll of 12 simple round would result into 11 ...)
            - SumAndRound::floor($requiredAmountOfMeals->getBonus()->getValue() / 2)
            + $this->getModifierByHuntingTime($huntingTime),
            $roll2D6DrdPlus
        );
    }

    /**
     * @param Time $time
     * @return int
     * @throws \DrdPlus\HuntingAndFishing\Exceptions\HuntingTimeIsTooShort
     */
    private function getModifierByHuntingTime(Time $time): int
    {
        $timeBonusValue = $time->getBonus()->getValue();
        if ($timeBonusValue < 45 /* roughly 30 minutes */) {
            throw new Exceptions\HuntingTimeIsTooShort(
                "You can not hunt for less than 30 minutes, got time for hunt only {$time}"
            );
        }
        return $timeBonusValue - self::STANDARD_HUNTING_TIME_IN_BONUS;
    }
}