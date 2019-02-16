<?php
namespace DrdPlus\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\SpecificAfflictions\Pain;

/**
 * @method static PainEffect getEnum($enumValue)
 */
class PainEffect extends AfflictionEffect
{
    public const PAIN_EFFECT = 'pain_effect';

    public static function getIt(): PainEffect
    {
        return static::getEnum(self::PAIN_EFFECT);
    }

    public function isEffectiveEvenOnSuccessAgainstTrap(): bool
    {
        return false;
    }

    public function getMalusFromPain(Pain $pain): int
    {
        return -$pain->getAfflictionSize()->getValue();
    }
}