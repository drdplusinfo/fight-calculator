<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\QualityAndSuccess;

use Granam\Strict\Object\StrictObject;

class ComparisonOfRollsOnSuccess extends StrictObject
{
    public static function isLesser(RollOnSuccess $lesserThan, RollOnSuccess $thatOne): bool
    {
        return ComparisonOfRollsOnQuality::isLesser($lesserThan->getRollOnQuality(), $thatOne->getRollOnQuality());
    }

    public static function isGreater(RollOnSuccess $greaterThan, RollOnSuccess $thatOne): bool
    {
        return ComparisonOfRollsOnQuality::isGreater($greaterThan->getRollOnQuality(), $thatOne->getRollOnQuality());
    }

    public static function isEqual(RollOnSuccess $equalTo, RollOnSuccess $thatOne): bool
    {
        return ComparisonOfRollsOnQuality::isEqual($equalTo->getRollOnQuality(), $thatOne->getRollOnQuality());
    }

    public static function compare(RollOnSuccess $compareThat, RollOnSuccess $withThat): int
    {
        return ComparisonOfRollsOnQuality::compare($compareThat->getRollOnQuality(), $withThat->getRollOnQuality());
    }
}