<?php
namespace DrdPlus\Health;

use Granam\IntegerEnum\IntegerEnum;
use Granam\Scalar\ScalarInterface;
use Granam\Tools\ValueDescriber;

/**
 * @method static TreatmentBoundary getEnum($value)
 */
class TreatmentBoundary extends IntegerEnum
{
    /**
     * @param int $value
     * @return TreatmentBoundary
     * @throws \DrdPlus\Health\Exceptions\TreatmentBoundaryCanNotBeNegative
     */
    public static function getIt($value): TreatmentBoundary
    {
        return static::getEnum($value);
    }

    /**
     * @param bool|float|int|ScalarInterface|string $enumValue
     * @throws \DrdPlus\Health\Exceptions\TreatmentBoundaryCanNotBeNegative
     * @return int
     */
    protected static function convertToEnumFinalValue($enumValue): int
    {
        try {
            $finalValue = parent::convertToEnumFinalValue($enumValue);
        } catch (\Granam\IntegerEnum\Exceptions\Exception $conversionException) {
            throw new Exceptions\TreatmentBoundaryCanNotBeNegative(
                'Expected integer as a wound value, got ' . ValueDescriber::describe($enumValue),
                $conversionException->getCode(),
                $conversionException
            );
        }
        if ($finalValue < 0) {
            throw new Exceptions\TreatmentBoundaryCanNotBeNegative(
                'Expected at least zero, got ' . ValueDescriber::describe($enumValue)
            );
        }
        return $finalValue;
    }
}