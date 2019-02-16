<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Native;

use DrdPlus\Codes\Properties\PropertyCode;

/**
 * @method static NativeRegeneration getIt($value)
 */
class NativeRegeneration extends NativeProperty
{
    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::NATIVE_REGENERATION);
    }
}