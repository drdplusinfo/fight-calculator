<?php
declare(strict_types=1);

namespace DrdPlus\Properties\RemarkableSenses;

use DrdPlus\Codes\Properties\PropertyCode;

class Smell extends RemarkableSenseProperty
{

    /**
     * @return Smell|RemarkableSenseProperty
     */
    public static function getIt(): Smell
    {
        return static::getEnum(PropertyCode::SMELL);
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::SMELL);
    }

}