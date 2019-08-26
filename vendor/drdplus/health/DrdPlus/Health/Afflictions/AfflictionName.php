<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions;

use Granam\String\StringInterface;
use Granam\StringEnum\StringEnum;
use Granam\Tools\ValueDescriber;

/**
 * @method static AfflictionName getEnum($value)
 */
class AfflictionName extends StringEnum
{
    /**
     * @param string|StringInterface $nameValue
     * @return AfflictionName
     */
    public static function getIt($nameValue): AfflictionName
    {
        return self::getEnum($nameValue);
    }

    /**
     * @param bool|float|int|string $enumValue
     * @return string
     * @throws \DrdPlus\Health\Afflictions\Exceptions\AfflictionNameCanNotBeEmpty
     */
    protected static function convertToEnumFinalValue($enumValue): string
    {
        $finalValue = parent::convertToEnumFinalValue($enumValue);
        if ($finalValue === '') {
            throw new Exceptions\AfflictionNameCanNotBeEmpty(
                'Name of an affliction has to have some value, got ' . ValueDescriber::describe($enumValue)
            );
        }
        return $finalValue;
    }

}