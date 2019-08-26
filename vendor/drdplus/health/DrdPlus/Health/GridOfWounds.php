<?php declare(strict_types=1);

namespace DrdPlus\Health;

use DrdPlus\Properties\Derived\WoundBoundary;
use DrdPlus\Calculations\SumAndRound;
use Granam\Strict\Object\StrictObject;

class GridOfWounds extends StrictObject
{

    public const PAIN_NUMBER_OF_ROWS = 1;
    public const UNCONSCIOUS_NUMBER_OF_ROWS = 2;
    public const TOTAL_NUMBER_OF_ROWS = 3;

    /**
     * @var Health
     */
    private $health;

    /**
     * @param Health $health
     */
    public function __construct(Health $health)
    {
        $this->health = $health;
    }

    /**
     * @param WoundBoundary $woundBoundary
     * @return int
     */
    public function getWoundsPerRowMaximum(WoundBoundary $woundBoundary): int
    {
        return $woundBoundary->getValue();
    }

    /**
     * @param WoundBoundary $woundBoundary
     * @param WoundSize $woundSize
     * @return int
     */
    public function calculateFilledHalfRowsFor(WoundSize $woundSize, WoundBoundary $woundBoundary): int
    {
        if ($this->getWoundsPerRowMaximum($woundBoundary) % 2 === 0) { // odd
            $filledHalfRows = SumAndRound::floor($woundSize->getValue() / ($this->getWoundsPerRowMaximum($woundBoundary) / 2));
        } else {
            // first half round up, second down (for example 11 = 6 + 5)
            $halves = [SumAndRound::ceiledHalf($this->getWoundsPerRowMaximum($woundBoundary)), SumAndRound::flooredHalf($this->getWoundsPerRowMaximum($woundBoundary))];
            $filledHalfRows = 0;
            $woundSizeValue = $woundSize->getValue();
            while ($woundSizeValue > 0) {
                foreach ($halves as $half) {
                    $woundSizeValue -= $half;
                    if ($woundSizeValue < 0) {
                        break;
                    }
                    $filledHalfRows++;
                }
            }
        }

        return $filledHalfRows < (self::TOTAL_NUMBER_OF_ROWS * 2)
            ? $filledHalfRows
            : self::TOTAL_NUMBER_OF_ROWS * 2; // to prevent "more dead than death" value
    }

    /**
     * @param WoundBoundary $woundBoundary
     * @return int
     */
    public function getNumberOfFilledRows(WoundBoundary $woundBoundary): int
    {
        $numberOfFilledRows = SumAndRound::floor($this->health->getUnhealedWoundsSum() / $this->getWoundsPerRowMaximum($woundBoundary));

        return $numberOfFilledRows < self::TOTAL_NUMBER_OF_ROWS
            ? $numberOfFilledRows
            : self::TOTAL_NUMBER_OF_ROWS;
    }

}