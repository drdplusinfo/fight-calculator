<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Body\WoundTypeCode;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Tables\Measurements\Distance\DistanceBonus;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Tables;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;

class NewWeaponService extends StrictObject
{
    /**
     * @param string $name
     * @param WeaponCategoryCode $meleeWeaponCategoryCode
     * @param Strength $requiredStrength
     * @param int $offensiveness
     * @param int $weaponLength
     * @param int $wounds
     * @param WoundTypeCode $woundTypeCode
     * @param int $cover
     * @param Weight $weight
     * @param bool $twoHandedOnly
     * @return bool if a weapon has been added or already exists
     * @throws Exceptions\NewWeaponNameCanNotBeEmpty
     * @throws \DrdPlus\Codes\Armaments\Exceptions\InvalidWeaponCategoryForNewMeleeWeaponCode
     * @throws \DrdPlus\Codes\Armaments\Exceptions\MeleeWeaponIsAlreadyInDifferentWeaponCategory
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeapon
     * @throws \DrdPlus\Tables\Armaments\Weapons\Exceptions\NewWeaponIsNotOfRequiredType
     * @throws \DrdPlus\Tables\Armaments\Weapons\Exceptions\DifferentWeaponIsUnderSameName
     */
    public function addNewMeleeWeapon(
        string $name,
        WeaponCategoryCode $meleeWeaponCategoryCode,
        Strength $requiredStrength,
        int $offensiveness,
        int $weaponLength,
        int $wounds,
        WoundTypeCode $woundTypeCode,
        int $cover,
        Weight $weight,
        bool $twoHandedOnly
    ): bool
    {
        if ($name === '') {
            throw new Exceptions\NewWeaponNameCanNotBeEmpty(
                'Given name for a new melee weapon is empty. Other provided parameters are:'
                . " weapon category $meleeWeaponCategoryCode, strength $requiredStrength,"
                . " offensiveness $offensiveness, length $weaponLength, wounds $wounds, wound type $woundTypeCode"
                . ", cover $cover, weight $weight, two-handed only " . ($twoHandedOnly ? 'yes' : 'no')
            );
        }
        $meleeWeaponCodeValue = StringTools::toConstant($name);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        MeleeWeaponCode::addNewMeleeWeaponCode($meleeWeaponCodeValue, $meleeWeaponCategoryCode, ['cs' => ['one' => $name]]);
        $meleeWeaponCode = MeleeWeaponCode::getIt($meleeWeaponCodeValue);
        return Tables::getIt()->getArmourer()->addNewMeleeWeapon(
            $meleeWeaponCode,
            $meleeWeaponCategoryCode,
            $requiredStrength,
            $weaponLength,
            $offensiveness,
            $wounds,
            $woundTypeCode,
            $cover,
            $weight,
            $twoHandedOnly
        );
    }

    /**
     * @param string $name
     * @param WeaponCategoryCode $rangedWeaponCategoryCode
     * @param Strength $requiredStrength
     * @param int $offensiveness
     * @param DistanceBonus $range
     * @param int $wounds
     * @param WoundTypeCode $woundTypeCode
     * @param int $cover
     * @param Weight $weight
     * @param bool $twoHandedOnly
     * @return bool if a weapon has been added or already exists
     * @throws Exceptions\NewWeaponNameCanNotBeEmpty
     * @throws \DrdPlus\Codes\Armaments\Exceptions\InvalidWeaponCategoryForNewRangedWeaponCode
     * @throws \DrdPlus\Codes\Armaments\Exceptions\RangedWeaponIsAlreadyInDifferentWeaponCategory
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownRangedWeapon
     * @throws \DrdPlus\Tables\Armaments\Weapons\Exceptions\NewWeaponIsNotOfRequiredType
     * @throws \DrdPlus\Tables\Armaments\Weapons\Exceptions\DifferentWeaponIsUnderSameName
     */
    public function addNewRangedWeapon(
        string $name,
        WeaponCategoryCode $rangedWeaponCategoryCode,
        Strength $requiredStrength,
        int $offensiveness,
        DistanceBonus $range,
        int $wounds,
        WoundTypeCode $woundTypeCode,
        int $cover,
        Weight $weight,
        bool $twoHandedOnly
    ): bool
    {
        if ($name === '') {
            throw new Exceptions\NewWeaponNameCanNotBeEmpty(
                'Given name for a new ranged weapon is empty. Other provided parameters are:'
                . " weapon category $rangedWeaponCategoryCode, strength $requiredStrength,"
                . " offensiveness $offensiveness, range $range, wounds $wounds, wound type $woundTypeCode"
                . ", cover $cover, weight $weight, two-handed only " . ($twoHandedOnly ? 'yes' : 'no')
            );
        }
        $rangedWeaponCodeValue = StringTools::toConstant($name);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        RangedWeaponCode::addNewRangedWeaponCode($rangedWeaponCodeValue, $rangedWeaponCategoryCode, ['cs' => ['one' => $name]]);
        $meleeWeaponCode = RangedWeaponCode::getIt($rangedWeaponCodeValue);
        return Tables::getIt()->getArmourer()->addNewRangedWeapon(
            $meleeWeaponCode,
            $rangedWeaponCategoryCode,
            $requiredStrength,
            $range,
            $offensiveness,
            $wounds,
            $woundTypeCode,
            $cover,
            $weight,
            $twoHandedOnly
        );
    }
}