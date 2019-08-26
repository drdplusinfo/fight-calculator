<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions\Effects;

/**
 * @method static HungerEffect getEnum($enumValue)
 */
class HungerEffect extends AfflictionEffect
{
    public const HUNGER_EFFECT = 'hunger_effect';

    public static function getIt(): HungerEffect
    {
        return static::getEnum(self::HUNGER_EFFECT);
    }

    public function isEffectiveEvenOnSuccessAgainstTrap(): bool
    {
        return true;
    }

}