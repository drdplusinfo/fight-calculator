<?php
namespace DrdPlus\Health\Afflictions;

use Granam\StringEnum\StringEnum;
use DrdPlus\Codes\Units\TimeUnitCode;
use Granam\Tools\ValueDescriber;

/**
 * @method static AfflictionVirulence getEnum($enumValue)
 */
class AfflictionVirulence extends StringEnum
{
    public const AFFLICTION_VIRULENCE = 'affliction_virulence';

    public const ROUND = TimeUnitCode::ROUND;

    public static function getRoundVirulence(): AfflictionVirulence
    {
        return static::getEnum(self::ROUND);
    }

    public const MINUTE = TimeUnitCode::MINUTE;

    public static function getMinuteVirulence(): AfflictionVirulence
    {
        return static::getEnum(TimeUnitCode::MINUTE);
    }

    public const HOUR = TimeUnitCode::HOUR;

    public static function getHourVirulence(): AfflictionVirulence
    {
        return static::getEnum(self::HOUR);
    }

    public const DAY = TimeUnitCode::DAY;

    public static function getDayVirulence(): AfflictionVirulence
    {
        return static::getEnum(self::DAY);
    }

    /**
     * @param bool|float|int|string|object $enumValue
     * @return string
     * @throws \DrdPlus\Health\Afflictions\Exceptions\UnknownVirulencePeriod
     */
    protected static function convertToEnumFinalValue($enumValue): string
    {
        $finalValue = parent::convertToEnumFinalValue($enumValue);
        if (!in_array($finalValue, [self::ROUND, self::MINUTE, self::HOUR, self::DAY], true)) {
            throw new Exceptions\UnknownVirulencePeriod(
                'Unknown period of a virulence: ' . ValueDescriber::describe($enumValue)
            );
        }
        return $finalValue;
    }

}