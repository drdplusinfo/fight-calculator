<?php
declare(strict_types=1);

namespace Granam\StringEnum;

use Granam\Scalar\Tools\ToString;
use Granam\ScalarEnum\ScalarEnum;
use Granam\String\StringInterface;

/**
 * @method static StringEnum getEnum($value)
 */
class StringEnum extends ScalarEnum implements StringInterface
{
    /**
     * @param bool|float|int|string|object $enumValue
     * @return string
     * @throws \Granam\StringEnum\Exceptions\WrongValueForStringEnum
     */
    protected static function convertToEnumFinalValue($enumValue): string
    {
        try {
            return ToString::toString($enumValue, true /* strict */);
        } catch (\Granam\Scalar\Tools\Exceptions\WrongParameterType $exception) {
            throw new Exceptions\WrongValueForStringEnum($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getValue(): string
    {
        return parent::getValue();
    }

}