<?php
declare(strict_types = 1);

namespace DrdPlus\Background\BackgroundParts;

use DrdPlus\Codes\History\AncestryCode;
use DrdPlus\Codes\History\ExceptionalityCode;
use DrdPlus\Background\BackgroundParts\Partials\AbstractBackgroundAdvantage;
use DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints;
use DrdPlus\Tables\History\Exceptions\UnexpectedBackgroundPoints;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveInteger;
use Granam\IntegerEnum\IntegerEnum;

class Ancestry extends AbstractBackgroundAdvantage
{
    /**
     * @param PositiveInteger $spentBackgroundPoints
     * @param Tables $tables
     * @return Ancestry|IntegerEnum
     * @throws \DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints
     */
    public static function getIt(PositiveInteger $spentBackgroundPoints, Tables $tables)
    {
        try {
            $tables->getAncestryTable()->getAncestryCodeByBackgroundPoints($spentBackgroundPoints);
        } catch (UnexpectedBackgroundPoints $unexpectedBackgroundPoints) {
            throw new TooMuchSpentBackgroundPoints($unexpectedBackgroundPoints->getMessage());
        }

        return self::getEnum($spentBackgroundPoints);
    }

    public static function getExceptionalityCode(): ExceptionalityCode
    {
        return ExceptionalityCode::getIt(ExceptionalityCode::ANCESTRY);
    }

    public function getAncestryCode(Tables $tables): AncestryCode
    {
        return $tables->getAncestryTable()->getAncestryCodeByBackgroundPoints($this->getSpentBackgroundPoints());
    }
}