<?php
namespace DrdPlus\Health;

use Granam\StringEnum\StringEnum;
use Granam\Scalar\ScalarInterface;
use Granam\Tools\ValueDescriber;

/**
 * @method static ReasonToRollAgainstMalusFromWounds getEnum($value)
 */
class ReasonToRollAgainstMalusFromWounds extends StringEnum
{
    public const WOUND = 'wound';

    public static function getWoundReason(): ReasonToRollAgainstMalusFromWounds
    {
        return static::getEnum(self::WOUND);
    }

    public function becauseOfWound(): bool
    {
        return $this->getValue() === self::WOUND;
    }

    public const HEAL = 'heal';

    public static function getHealReason(): ReasonToRollAgainstMalusFromWounds
    {
        return static::getEnum(self::HEAL);
    }

    public function becauseOfHeal(): bool
    {
        return $this->getValue() === self::HEAL;
    }

    /**
     * @param string $reasonCode
     * @return ReasonToRollAgainstMalusFromWounds
     * @throws \DrdPlus\Health\Exceptions\UnknownReasonToRollAgainstMalus
     */
    public static function getIt($reasonCode): ReasonToRollAgainstMalusFromWounds
    {
        return static::getEnum($reasonCode);
    }

    /**
     * @param bool|float|int|ScalarInterface|string $enumValue
     * @return string
     * @throws \DrdPlus\Health\Exceptions\UnknownReasonToRollAgainstMalus
     */
    protected static function convertToEnumFinalValue($enumValue): string
    {
        $finalValue = parent::convertToEnumFinalValue($enumValue);
        if ($finalValue !== self::WOUND && $finalValue !== self::HEAL) {
            throw new Exceptions\UnknownReasonToRollAgainstMalus(
                'Expected one of ' . self::WOUND . ' or ' . self::HEAL . ', got ' . ValueDescriber::describe($enumValue)
            );
        }
        return $finalValue;
    }

}