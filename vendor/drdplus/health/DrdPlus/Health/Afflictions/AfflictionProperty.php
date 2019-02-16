<?php
namespace DrdPlus\Health\Afflictions;

use DrdPlus\Codes\Properties\PropertyCode;
use Granam\String\StringInterface;
use Granam\StringEnum\StringEnum;
use Granam\Tools\ValueDescriber;

/**
 * @method static AfflictionProperty getEnum($value)
 */
class AfflictionProperty extends StringEnum
{
    /**
     * @param string|StringInterface $propertyCode
     * @return AfflictionProperty
     * @throws \DrdPlus\Health\Afflictions\Exceptions\UnknownAfflictionPropertyCode
     */
    public static function getIt($propertyCode): AfflictionProperty
    {
        return self::getEnum($propertyCode);
    }

    /**
     * @param string|StringInterface $enumValue
     * @return string
     * @throws \DrdPlus\Health\Afflictions\Exceptions\UnknownAfflictionPropertyCode
     */
    protected static function convertToEnumFinalValue($enumValue): string
    {
        $enumFinalValue = parent::convertToEnumFinalValue($enumValue);
        if (!in_array($enumFinalValue, self::getProperties(), true)) {
            throw new Exceptions\UnknownAfflictionPropertyCode(
                'Got unknown code of property keeping affliction on short: '
                . ValueDescriber::describe($enumValue)
            );
        }

        return $enumFinalValue;
    }

    public const STRENGTH = PropertyCode::STRENGTH;
    public const AGILITY = PropertyCode::AGILITY;
    public const KNACK = PropertyCode::KNACK;
    public const WILL = PropertyCode::WILL;
    public const INTELLIGENCE = PropertyCode::INTELLIGENCE;
    public const CHARISMA = PropertyCode::CHARISMA;
    public const ENDURANCE = PropertyCode::ENDURANCE;
    public const TOUGHNESS = PropertyCode::TOUGHNESS;
    public const LEVEL = 'level';

    /**
     * @return array|string[]
     */
    public static function getProperties(): array
    {
        return [
            self::STRENGTH,
            self::AGILITY,
            self::KNACK,
            self::WILL,
            self::INTELLIGENCE,
            self::CHARISMA,
            self::ENDURANCE,
            self::TOUGHNESS,
            self::LEVEL,
        ];
    }

}