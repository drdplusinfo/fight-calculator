<?php
declare(strict_types=1);

namespace DrdPlus\Background\BackgroundParts\Partials;

use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveInteger;
use Granam\IntegerEnum\IntegerEnum;

abstract class AbstractAncestryDependent extends AbstractBackgroundAdvantage
{
    /**
     * @param PositiveInteger $spentBackgroundPoints
     * @param Ancestry $ancestry
     * @param Tables $tables
     * @return AbstractAncestryDependent|IntegerEnum
     * @throws \DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints
     */
    protected static function createIt(PositiveInteger $spentBackgroundPoints, Ancestry $ancestry, Tables $tables)
    {
        $maxPointsToDistribute = $tables->getBackgroundPointsDistributionTable()->getMaxPointsToDistribute(
            static::getExceptionalityCode(),
            $tables->getAncestryTable(),
            $ancestry->getAncestryCode($tables)
        );
        if ($maxPointsToDistribute < $spentBackgroundPoints->getValue()) {
            throw new TooMuchSpentBackgroundPoints(
                static::class . " can not use more points than $maxPointsToDistribute"
                . ' (' . ($maxPointsToDistribute - $ancestry->getSpentBackgroundPoints()->getValue()) . ' more than ancestry)'
                . ', got ' . $spentBackgroundPoints
            );
        }

        return self::getEnum($spentBackgroundPoints);
    }
}