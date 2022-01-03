<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions;

use Granam\Integer\IntegerInterface;
use Granam\IntegerEnum\IntegerEnum;
use Granam\Tools\ValueDescriber;

/**
 * @method static AfflictionSize getEnum($value)
 */
class AfflictionSize extends IntegerEnum
{
    /**
     * @param int|IntegerInterface $size
     * @return AfflictionSize
     * @throws \DrdPlus\Health\Afflictions\Exceptions\AfflictionSizeCanNotBeNegative
     */
    public static function getIt($size): AfflictionSize
    {
        return self::getEnum($size);
    }

    /**
     * @param mixed $enumValue
     * @return int
     * @throws \DrdPlus\Health\Afflictions\Exceptions\AfflictionSizeCanNotBeNegative
     */
    protected static function convertToEnumFinalValue($enumValue): int
    {
        $finalValue = parent::convertToEnumFinalValue($enumValue);
        if ($finalValue < 0) {
            throw new Exceptions\AfflictionSizeCanNotBeNegative(
                'Affliction size has to be at least 0, got ' . ValueDescriber::describe($enumValue)
            );
        }
        return $finalValue;
    }

}