<?php
declare(strict_types=1);

namespace Granam\FloatEnum;

use Granam\Float\Tools\ToFloat;
use Granam\ScalarEnum\ScalarEnum;

/**
 * @method static FloatEnum getEnum($enumValue)
 */
class FloatEnum extends ScalarEnum implements FloatEnumInterface
{
    public function getValue(): float
    {
        return parent::getValue();
    }

    /**
     * Overloaded parent @see \Granam\Scalar\EnumTrait::convertToEnumFinalValue
     *
     * @param $value
     * @return float
     * @throws \Granam\FloatEnum\Exceptions\WrongValueForFloatEnum
     */
    protected static function convertToEnumFinalValue($value): float
    {
        try {
            return ToFloat::toFloat($value, true /* strict */);
        } catch (\Granam\Float\Tools\Exceptions\WrongParameterType $exception) {
            throw new Exceptions\WrongValueForFloatEnum($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
