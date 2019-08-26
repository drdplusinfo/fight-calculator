<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Native;

use DrdPlus\BaseProperties\Property;
use Granam\Boolean\BooleanInterface;
use Granam\BooleanEnum\BooleanEnum;

abstract class NativeProperty extends BooleanEnum implements Property
{
    /**
     * @param bool|BooleanInterface $value
     * @return NativeProperty
     * @throws \Granam\BooleanEnum\Exceptions\WrongValueForBooleanEnum
     */
    public static function getIt($value): NativeProperty
    {
        return new static($value);
    }
}