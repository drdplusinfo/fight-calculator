<?php
namespace DrdPlus\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\SpecificAfflictions\CrackedBones;

/**
 * @method static CrackedBonesEffect getEnum($enumValue)
 */
class CrackedBonesEffect extends AfflictionEffect
{
    public const CRACKED_BONES_EFFECT = 'cracked_bones_effect';

    public static function getIt(): CrackedBonesEffect
    {
        return static::getEnum(self::CRACKED_BONES_EFFECT);
    }

    public function isEffectiveEvenOnSuccessAgainstTrap(): bool
    {
        return true;
    }

    public function getHealingMalus(CrackedBones $crackedBones): int
    {
        // note: affliction size is always at least zero, therefore this malus is at least zero or less
        return -$crackedBones->getAfflictionSize()->getValue();
    }

}