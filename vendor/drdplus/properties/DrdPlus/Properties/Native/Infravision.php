<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Native;

use DrdPlus\Codes\Properties\PropertyCode;

/**
 * @method static Infravision getIt($value)
 */
class Infravision extends NativeProperty
{
    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::INFRAVISION);
    }
}