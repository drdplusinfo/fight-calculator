<?php
declare(strict_types=1);

namespace DrdPlus\Properties\RemarkableSenses;

use DrdPlus\Codes\Properties\PropertyCode;

class Sight extends RemarkableSenseProperty
{

    /**
     * @return Sight|RemarkableSenseProperty
     */
    public static function getIt(): Sight
    {
        return static::getEnum(PropertyCode::SIGHT);
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::SIGHT);
    }

}