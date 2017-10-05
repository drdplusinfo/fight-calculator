<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Body\WoundTypeCode;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Tables\Measurements\Distance\DistanceBonus;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveInteger;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;

class CustomArmamentsService extends StrictObject
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
     * @throws Exceptions\NameOfCustomWeaponNameCanNotBeEmpty
     * @throws \DrdPlus\Codes\Armaments\Exceptions\InvalidWeaponCategoryForNewMeleeWeaponCode
     * @throws \DrdPlus\Codes\Armaments\Exceptions\MeleeWeaponIsAlreadyInDifferentWeaponCategory
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeapon
     * @throws \DrdPlus\Tables\Armaments\Weapons\Exceptions\NewWeaponIsNotOfRequiredType
     * @throws \DrdPlus\Tables\Armaments\Weapons\Exceptions\DifferentWeaponIsUnderSameName
     */
    public function addCustomMeleeWeapon(
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
            throw new Exceptions\NameOfCustomWeaponNameCanNotBeEmpty(
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

        return Tables::getIt()->getArmourer()->addCustomMeleeWeapon(
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
     * @throws Exceptions\NameOfCustomWeaponNameCanNotBeEmpty
     * @throws \DrdPlus\Codes\Armaments\Exceptions\InvalidWeaponCategoryForNewRangedWeaponCode
     * @throws \DrdPlus\Codes\Armaments\Exceptions\RangedWeaponIsAlreadyInDifferentWeaponCategory
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownRangedWeapon
     * @throws \DrdPlus\Tables\Armaments\Weapons\Exceptions\NewWeaponIsNotOfRequiredType
     * @throws \DrdPlus\Tables\Armaments\Weapons\Exceptions\DifferentWeaponIsUnderSameName
     */
    public function addCustomRangedWeapon(
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
            throw new Exceptions\NameOfCustomWeaponNameCanNotBeEmpty(
                'Given name for a new ranged weapon is empty. Other provided parameters are:'
                . " weapon category $rangedWeaponCategoryCode, strength $requiredStrength,"
                . " offensiveness $offensiveness, range $range, wounds $wounds, wound type $woundTypeCode"
                . ", cover $cover, weight $weight, two-handed only " . ($twoHandedOnly ? 'yes' : 'no')
            );
        }
        $rangedWeaponCodeValue = StringTools::toConstant($name);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        RangedWeaponCode::addNewRangedWeaponCode($rangedWeaponCodeValue, $rangedWeaponCategoryCode, ['cs' => ['one' => $name]]);
        $rangedWeaponCode = RangedWeaponCode::getIt($rangedWeaponCodeValue);

        return Tables::getIt()->getArmourer()->addCustomRangedWeapon(
            $rangedWeaponCode,
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

    /**
     * @param string $name
     * @param Strength $requiredStrength
     * @param int $restriction
     * @param int $protection
     * @param Weight $weight
     * @param PositiveInteger $roundsToPutOn
     * @return bool
     * @throws \DrdPlus\Fight\Exceptions\NameOfCustomBodyArmorCanNotBeEmpty
     * @throws \DrdPlus\Tables\Armaments\Armors\Exceptions\DifferentBodyArmorIsUnderSameName
     */
    public function addCustomBodyArmor(
        string $name,
        Strength $requiredStrength,
        int $restriction,
        int $protection,
        Weight $weight,
        PositiveInteger $roundsToPutOn
    ): bool
    {
        if ($name === '') {
            throw new Exceptions\NameOfCustomBodyArmorCanNotBeEmpty(
                'Given name for a custom body armor is empty. Other provided parameters are:'
                . " required strength $requiredStrength, restriction $restriction, protection $protection, weight $weight"
                . ", rounds to put on $roundsToPutOn"
            );
        }
        $bodyArmorWeaponValue = StringTools::toConstant($name);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        BodyArmorCode::addNewBodyArmorCode($bodyArmorWeaponValue, ['cs' => ['one' => $name]]);
        $bodyArmorWeaponCode = BodyArmorCode::getIt($bodyArmorWeaponValue);

        return Tables::getIt()->getArmourer()->addCustomBodyArmor(
            $bodyArmorWeaponCode,
            $requiredStrength,
            $restriction,
            $protection,
            $weight,
            $roundsToPutOn
        );
    }

    /**
     * @param string $name
     * @param Strength $requiredStrength
     * @param int $restriction
     * @param int $protection
     * @param Weight $weight
     * @return bool
     * @throws \DrdPlus\Fight\Exceptions\NameOfCustomHelmCanNotBeEmpty
     * @throws \DrdPlus\Tables\Armaments\Armors\Exceptions\DifferentHelmIsUnderSameName
     */
    public function addCustomHelm(
        string $name,
        Strength $requiredStrength,
        int $restriction,
        int $protection,
        Weight $weight
    ): bool
    {
        if ($name === '') {
            throw new Exceptions\NameOfCustomHelmCanNotBeEmpty(
                'Given name for a custom helm is empty. Other provided parameters are:'
                . " required strength $requiredStrength, protection $protection, weight $weight"
            );
        }
        $helmCodeValue = StringTools::toConstant($name);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        HelmCode::addNewHelmCode($helmCodeValue, ['cs' => ['one' => $name]]);
        $helmCode = HelmCode::getIt($helmCodeValue);

        return Tables::getIt()->getArmourer()->addCustomHelm(
            $helmCode,
            $requiredStrength,
            $restriction,
            $protection,
            $weight
        );
    }
}