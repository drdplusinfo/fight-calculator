<?php
namespace DrdPlus\Calculators\Fight;

use Granam\Strict\Object\StrictObject;

class CurrentValues extends StrictObject
{
    // melee weapon
    const CUSTOM_MELEE_WEAPON_NAME = 'custom_melee_weapon_name';
    const CUSTOM_MELEE_WEAPON_CATEGORY = 'custom_melee_weapon_category';
    const CUSTOM_MELEE_WEAPON_REQUIRED_STRENGTH = 'custom_melee_weapon_required_strength';
    const CUSTOM_MELEE_WEAPON_LENGTH = 'custom_melee_weapon_length';
    const CUSTOM_MELEE_WEAPON_OFFENSIVENESS = 'custom_melee_weapon_offensiveness';
    const CUSTOM_MELEE_WEAPON_WOUNDS = 'custom_melee_weapon_wounds';
    const CUSTOM_MELEE_WEAPON_WOUND_TYPE = 'custom_melee_weapon_wound_type';
    const CUSTOM_MELEE_WEAPON_COVER = 'custom_melee_weapon_cover';
    const CUSTOM_MELEE_WEAPON_WEIGHT = 'custom_melee_weapon_weight';
    const CUSTOM_MELEE_WEAPON_TWO_HANDED_ONLY = 'custom_melee_weapon_two_handed_only';
    // ranged weapon
    const CUSTOM_RANGED_WEAPON_NAME = 'custom_ranged_weapon_name';
    const CUSTOM_RANGED_WEAPON_CATEGORY = 'custom_ranged_weapon_category';
    const CUSTOM_RANGED_WEAPON_REQUIRED_STRENGTH = 'custom_ranged_weapon_required_strength';
    const CUSTOM_RANGED_WEAPON_RANGE_IN_M = 'custom_ranged_weapon_range_in_m';
    const CUSTOM_RANGED_WEAPON_OFFENSIVENESS = 'custom_ranged_weapon_offensiveness';
    const CUSTOM_RANGED_WEAPON_WOUNDS = 'custom_ranged_weapon_wounds';
    const CUSTOM_RANGED_WEAPON_WOUND_TYPE = 'custom_ranged_weapon_wound_type';
    const CUSTOM_RANGED_WEAPON_COVER = 'custom_ranged_weapon_cover';
    const CUSTOM_RANGED_WEAPON_WEIGHT = 'custom_ranged_weapon_weight';
    const CUSTOM_RANGED_WEAPON_TWO_HANDED_ONLY = 'custom_ranged_weapon_two_handed_only';
    // body armor
    const CUSTOM_BODY_ARMOR_NAME = 'custom_body_armor_name';
    const CUSTOM_BODY_ARMOR_REQUIRED_STRENGTH = 'custom_body_armor_required_strength';
    const CUSTOM_BODY_ARMOR_RESTRICTION = 'custom_body_armor_restriction';
    const CUSTOM_BODY_ARMOR_PROTECTION = 'custom_body_armor_protection';
    const CUSTOM_BODY_ARMOR_WEIGHT = 'custom_body_armor_weight';
    const CUSTOM_BODY_ARMOR_ROUNDS_TO_PUT_ON = 'custom_body_armor_rounds_to_put_on';
    // helm
    const CUSTOM_HELM_NAME = 'custom_helm_name';
    const CUSTOM_HELM_REQUIRED_STRENGTH = 'custom_helm_required_strength';
    const CUSTOM_HELM_RESTRICTION = 'custom_helm_restriction';
    const CUSTOM_HELM_PROTECTION = 'custom_helm_protection';
    const CUSTOM_HELM_WEIGHT = 'custom_helm_weight';

    /** @var array */
    private $valuesFromInput;
    /** @var PreviousValues */
    private $historyValues;
    /** @var array */
    private $customRangedWeaponsValues;
    /** @var array */
    private $customMeleeWeaponsValues;
    /** @var array */
    private $customBodyArmorsValues;
    /** @var array */
    private $customHelmsValues;

    /**
     * @param array $valuesFromInput
     * @param PreviousValues $historyValues
     */
    public function __construct(array $valuesFromInput, PreviousValues $historyValues)
    {
        $this->valuesFromInput = $valuesFromInput;
        $this->historyValues = $historyValues;
    }

    /**
     * @param string $name
     * @return string|string[]|null
     */
    public function getValue(string $name)
    {
        if (array_key_exists($name, $this->valuesFromInput)) {
            return $this->valuesFromInput[$name];
        }

        return $this->historyValues->getValue($name);
    }

    /**
     * @param string $name
     * @return null|string[]|array|string
     */
    public function getCurrentValue(string $name)
    {
        return $this->valuesFromInput[$name] ?? null;
    }

    /**
     * @return array|string[][]
     */
    public function getCustomMeleeWeaponsValues(): array
    {
        if ($this->customMeleeWeaponsValues !== null) {
            return $this->customMeleeWeaponsValues;
        }
        $this->customMeleeWeaponsValues = $this->assembleCustomMeleeWeaponsValues();

        return $this->customMeleeWeaponsValues;
    }

    private function assembleCustomMeleeWeaponsValues(): array
    {
        return $this->assembleCustomArmamentsValues(
            [
                self::CUSTOM_MELEE_WEAPON_NAME,
                self::CUSTOM_MELEE_WEAPON_CATEGORY,
                self::CUSTOM_MELEE_WEAPON_OFFENSIVENESS,
                self::CUSTOM_MELEE_WEAPON_LENGTH,
                self::CUSTOM_MELEE_WEAPON_REQUIRED_STRENGTH,
                self::CUSTOM_MELEE_WEAPON_WOUND_TYPE,
                self::CUSTOM_MELEE_WEAPON_WOUNDS,
                self::CUSTOM_MELEE_WEAPON_COVER,
                self::CUSTOM_MELEE_WEAPON_WEIGHT,
                self::CUSTOM_MELEE_WEAPON_TWO_HANDED_ONLY,
            ],
            self::CUSTOM_MELEE_WEAPON_NAME,
            self::CUSTOM_MELEE_WEAPON_TWO_HANDED_ONLY
        );
    }

    private function assembleCustomArmamentsValues(
        array $customWeaponKeys,
        string $customWeaponNameKey,
        string $customWeaponTwoHandedOnlyKey
    ): array
    {
        $nameIndexedValues = [];
        $weaponNames = (array)$this->getValue($customWeaponNameKey);
        foreach ($weaponNames as $index => $weaponName) {
            $customWeapon = [];
            foreach ($customWeaponKeys as $typeName) {
                $sameTypeValues = $typeName === $customWeaponNameKey
                    ? $weaponNames
                    : (array)$this->getValue($typeName);
                if ($typeName === $customWeaponTwoHandedOnlyKey) {
                    $sameTypeValues[$index] = (bool)($sameTypeValues[$index] ?? false);
                } else {
                    if (($sameTypeValues[$index] ?? null) === null) {
                        throw new Exceptions\BrokenNewWeaponValues(
                            "Missing '{$typeName}' on index '{$index}' for a new weapon '{$weaponName}'"
                        );
                    }
                }
                $customWeapon[$typeName] = $sameTypeValues[$index];
            }
            // re-index everything from integer index to weapon name
            $nameIndexedValues[$weaponName] = $customWeapon;
        }

        return $nameIndexedValues;
    }

    /**
     * @return array|string[][]
     */
    public function getCustomRangedWeaponsValues(): array
    {
        if ($this->customRangedWeaponsValues !== null) {
            return $this->customRangedWeaponsValues;
        }
        $this->customRangedWeaponsValues = $this->assembleCustomRangedWeaponsValues();

        return $this->customRangedWeaponsValues;
    }

    private function assembleCustomRangedWeaponsValues(): array
    {
        return $this->assembleCustomArmamentsValues(
            [
                self::CUSTOM_RANGED_WEAPON_NAME,
                self::CUSTOM_RANGED_WEAPON_CATEGORY,
                self::CUSTOM_RANGED_WEAPON_OFFENSIVENESS,
                self::CUSTOM_RANGED_WEAPON_RANGE_IN_M,
                self::CUSTOM_RANGED_WEAPON_REQUIRED_STRENGTH,
                self::CUSTOM_RANGED_WEAPON_WOUND_TYPE,
                self::CUSTOM_RANGED_WEAPON_WOUNDS,
                self::CUSTOM_RANGED_WEAPON_COVER,
                self::CUSTOM_RANGED_WEAPON_WEIGHT,
                self::CUSTOM_RANGED_WEAPON_TWO_HANDED_ONLY,
            ],
            self::CUSTOM_RANGED_WEAPON_NAME,
            self::CUSTOM_RANGED_WEAPON_TWO_HANDED_ONLY
        );
    }

    /**
     * @return array|string[][]
     */
    public function getCustomBodyArmorsValues(): array
    {
        if ($this->customBodyArmorsValues !== null) {
            return $this->customBodyArmorsValues;
        }
        $this->customBodyArmorsValues = $this->assembleCustomBodyArmorsValues();

        return $this->customBodyArmorsValues;
    }

    private function assembleCustomBodyArmorsValues(): array
    {
        return $this->assembleCustomArmamentsValues(
            [
                self::CUSTOM_BODY_ARMOR_NAME,
                self::CUSTOM_BODY_ARMOR_REQUIRED_STRENGTH,
                self::CUSTOM_BODY_ARMOR_RESTRICTION,
                self::CUSTOM_BODY_ARMOR_PROTECTION,
                self::CUSTOM_BODY_ARMOR_WEIGHT,
                self::CUSTOM_BODY_ARMOR_ROUNDS_TO_PUT_ON,
            ],
            self::CUSTOM_BODY_ARMOR_NAME,
            false /* no boolean parameter */
        );
    }

    /**
     * @return array|string[][]
     */
    public function getCustomHelmsValues(): array
    {
        if ($this->customHelmsValues !== null) {
            return $this->customHelmsValues;
        }
        $this->customHelmsValues = $this->assembleCustomHelmsValues();

        return $this->customHelmsValues;
    }

    private function assembleCustomHelmsValues(): array
    {
        return $this->assembleCustomArmamentsValues(
            [
                self::CUSTOM_HELM_NAME,
                self::CUSTOM_HELM_REQUIRED_STRENGTH,
                self::CUSTOM_HELM_RESTRICTION,
                self::CUSTOM_HELM_PROTECTION,
                self::CUSTOM_HELM_WEIGHT,
            ],
            self::CUSTOM_HELM_NAME,
            false /* no boolean parameter */
        );
    }
}