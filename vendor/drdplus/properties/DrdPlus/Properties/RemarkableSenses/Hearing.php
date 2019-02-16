<?php
declare(strict_types=1);

namespace DrdPlus\Properties\RemarkableSenses;

use DrdPlus\Codes\Properties\PropertyCode;

class Hearing extends RemarkableSenseProperty
{
    /**
     * @return Hearing|RemarkableSenseProperty
     */
    public static function getIt(): Hearing
    {
        return static::getEnum(PropertyCode::HEARING);
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::HEARING);
    }

}