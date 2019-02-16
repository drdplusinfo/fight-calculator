<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\QualityAndSuccess;

use Granam\Strict\Object\StrictObject;

class ComparisonOfRollsOnQuality extends StrictObject
{
    public static function isLesser(RollOnQuality $lesserThan, RollOnQuality $thatOne): bool
    {
        return $lesserThan->getValue() < $thatOne->getValue();
    }

    public static function isGreater(RollOnQuality $greaterThan, RollOnQuality $thatOne): bool
    {
        return $greaterThan->getValue() > $thatOne->getValue();
    }

    public static function isEqual(RollOnQuality $equalTo, RollOnQuality $thatOne): bool
    {
        return $equalTo->getValue() === $thatOne->getValue();
    }

    public static function compare(RollOnQuality $compareThat, RollOnQuality $withThat): int
    {
        return $compareThat->getValue() <=> $withThat->getValue();
    }
}