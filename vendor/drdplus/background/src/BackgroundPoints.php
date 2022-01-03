<?php
declare(strict_types = 1);

namespace DrdPlus\Background;

use DrdPlus\Codes\History\FateCode;
use DrdPlus\Tables\Tables;
use Granam\IntegerEnum\IntegerEnum;

class BackgroundPoints extends IntegerEnum
{
    /**
     * @param FateCode $fateCode
     * @param Tables $tables
     * @return BackgroundPoints|IntegerEnum
     */
    public static function getIt(FateCode $fateCode, Tables $tables): BackgroundPoints
    {
        return static::getEnum($tables->getBackgroundPointsTable()->getBackgroundPointsByPlayerDecision($fateCode));
    }
}