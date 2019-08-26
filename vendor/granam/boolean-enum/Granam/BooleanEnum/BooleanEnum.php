<?php declare(strict_types=1);

namespace Granam\BooleanEnum;

use Granam\Boolean\Tools\ToBoolean;
use Granam\ScalarEnum\ScalarEnum;

/**
 * @method static BooleanEnum getEnum($value)
 */
class BooleanEnum extends ScalarEnum implements BooleanEnumInterface
{

    /**
     * Overloading parent @see \Granam\ScalarEnum\ScalarEnum::convertToEnumFinalValue
     *
     * @param mixed $enumValue
     * @return bool
     * @throws \Granam\BooleanEnum\Exceptions\WrongValueForBooleanEnum
     */
    protected static function convertToEnumFinalValue($enumValue): bool
    {
        try {
            return ToBoolean::toBoolean($enumValue, true /* strict */);
        } catch (\Granam\Boolean\Tools\Exceptions\WrongParameterType $exception) {
            // wrapping the exception by local one
            throw new Exceptions\WrongValueForBooleanEnum($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getValue(): bool
    {
        return parent::getValue();
    }
}