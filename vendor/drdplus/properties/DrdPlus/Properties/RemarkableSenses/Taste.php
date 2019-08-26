<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\RemarkableSenses;

use DrdPlus\Codes\Properties\PropertyCode;

class Taste extends RemarkableSenseProperty
{

    /**
     * @return Taste|RemarkableSenseProperty
     */
    public static function getIt(): Taste
    {
        return static::getEnum(PropertyCode::TASTE);
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::TASTE);
    }
}