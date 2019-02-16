<?php
namespace DrdPlus\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\SpecificAfflictions\Cold;
use DrdPlus\Calculations\SumAndRound;

/**
 * @method static ColdEffect getEnum($enumValue)
 */
class ColdEffect extends AfflictionEffect
{
    public const COLD_EFFECT = 'cold_effect';

    public static function getIt(): ColdEffect
    {
        return static::getEnum(self::COLD_EFFECT);
    }

    public function isEffectiveEvenOnSuccessAgainstTrap(): bool
    {
        return false;
    }

    public function getStrengthMalus(Cold $cold): int
    {
        return -SumAndRound::ceil($cold->getAfflictionSize()->getValue() / 4);
    }

    public function getAgilityMalus(Cold $cold): int
    {
        return -SumAndRound::ceil($cold->getAfflictionSize()->getValue() / 4);
    }

    public function getKnackMalus(Cold $cold): int
    {
        return -SumAndRound::ceil($cold->getAfflictionSize()->getValue() / 4);
    }
}