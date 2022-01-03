<?php declare(strict_types=1);

namespace Granam\IntegerEnum;

use Granam\Integer\Tools\ToInteger;
use Granam\ScalarEnum\ScalarEnum;

/**
 * @method static IntegerEnum getEnum($value)
 */
class IntegerEnum extends ScalarEnum implements IntegerEnumInterface
{

    /**
     * Overloading parent @see \Granam\Scalar\EnumTrait::convertToEnumFinalValue
     *
     * @param mixed $enumValue
     * @return int
     * @throws \Granam\IntegerEnum\Exceptions\WrongValueForIntegerEnum
     */
    protected static function convertToEnumFinalValue($enumValue): int
    {
        try {
            return ToInteger::toInteger($enumValue, true /* strict */);
        } catch (\Granam\Integer\Tools\Exceptions\WrongParameterType $exception) {
            // wrapping the exception by local one
            throw new Exceptions\WrongValueForIntegerEnum($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getValue(): int
    {
        return parent::getValue();
    }

}
