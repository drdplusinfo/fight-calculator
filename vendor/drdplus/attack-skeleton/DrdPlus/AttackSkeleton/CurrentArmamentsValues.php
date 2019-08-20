<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\CalculatorSkeleton\CurrentValues;
use Granam\Strict\Object\StrictObject;

class CurrentArmamentsValues extends StrictObject
{
    // melee weapon
    public const CUSTOM_MELEE_WEAPON_NAME = 'custom_melee_weapon_name';
    public const CUSTOM_MELEE_WEAPON_CATEGORY = 'custom_melee_weapon_category';
    public const CUSTOM_MELEE_WEAPON_REQUIRED_STRENGTH = 'custom_melee_weapon_required_strength';
    public const CUSTOM_MELEE_WEAPON_LENGTH = 'custom_melee_weapon_length';
    public const CUSTOM_MELEE_WEAPON_OFFENSIVENESS = 'custom_melee_weapon_offensiveness';
    public const CUSTOM_MELEE_WEAPON_WOUNDS = 'custom_melee_weapon_wounds';
    public const CUSTOM_MELEE_WEAPON_WOUND_TYPE = 'custom_melee_weapon_wound_type';
    public const CUSTOM_MELEE_WEAPON_COVER = 'custom_melee_weapon_cover';
    public const CUSTOM_MELEE_WEAPON_WEIGHT = 'custom_melee_weapon_weight';
    public const CUSTOM_MELEE_WEAPON_TWO_HANDED_ONLY = 'custom_melee_weapon_two_handed_only';
    // ranged weapon
    public const CUSTOM_RANGED_WEAPON_NAME = 'custom_ranged_weapon_name';
    public const CUSTOM_RANGED_WEAPON_CATEGORY = 'custom_ranged_weapon_category';
    public const CUSTOM_RANGED_WEAPON_OFFENSIVENESS = 'custom_ranged_weapon_offensiveness';
    public const CUSTOM_RANGED_WEAPON_RANGE_IN_M = 'custom_ranged_weapon_range_in_m';
    public const CUSTOM_RANGED_WEAPON_REQUIRED_STRENGTH = 'custom_ranged_weapon_required_strength';
    public const CUSTOM_RANGED_WEAPON_WOUND_TYPE = 'custom_ranged_weapon_wound_type';
    public const CUSTOM_RANGED_WEAPON_WOUNDS = 'custom_ranged_weapon_wounds';
    public const CUSTOM_RANGED_WEAPON_COVER = 'custom_ranged_weapon_cover';
    public const CUSTOM_RANGED_WEAPON_WEIGHT = 'custom_ranged_weapon_weight';
    public const CUSTOM_RANGED_WEAPON_TWO_HANDED_ONLY = 'custom_ranged_weapon_two_handed_only';
    public const CUSTOM_RANGED_WEAPON_MAXIMAL_APPLICABLE_STRENGTH = 'custom_ranged_weapon_maximal_applicable_strength';
    // body armor
    public const CUSTOM_BODY_ARMOR_NAME = 'custom_body_armor_name';
    public const CUSTOM_BODY_ARMOR_REQUIRED_STRENGTH = 'custom_body_armor_required_strength';
    public const CUSTOM_BODY_ARMOR_RESTRICTION = 'custom_body_armor_restriction';
    public const CUSTOM_BODY_ARMOR_PROTECTION = 'custom_body_armor_protection';
    public const CUSTOM_BODY_ARMOR_WEIGHT = 'custom_body_armor_weight';
    public const CUSTOM_BODY_ARMOR_ROUNDS_TO_PUT_ON = 'custom_body_armor_rounds_to_put_on';
    // helm
    public const CUSTOM_HELM_NAME = 'custom_helm_name';
    public const CUSTOM_HELM_REQUIRED_STRENGTH = 'custom_helm_required_strength';
    public const CUSTOM_HELM_RESTRICTION = 'custom_helm_restriction';
    public const CUSTOM_HELM_PROTECTION = 'custom_helm_protection';
    public const CUSTOM_HELM_WEIGHT = 'custom_helm_weight';
    // shield
    public const CUSTOM_SHIELD_NAME = 'custom_shield_name';
    public const CUSTOM_SHIELD_REQUIRED_STRENGTH = 'custom_shield_required_strength';
    public const CUSTOM_SHIELD_RESTRICTION = 'custom_shield_restriction';
    public const CUSTOM_SHIELD_COVER = 'custom_shield_cover';
    public const CUSTOM_SHIELD_WEIGHT = 'custom_shield_weight';
    public const CUSTOM_SHIELD_TWO_HANDED_ONLY = 'custom_shield_two_handed_only';

    /** @var CurrentValues */
    private $currentValues;
    /** @var array */
    private $currentCustomRangedWeaponsValues;
    /** @var array */
    private $currentCustomMeleeWeaponsValues;
    /** @var array */
    private $currentCustomBodyArmorsValues;
    /** @var array */
    private $currentCustomHelmsValues;
    /** @var array */
    private $currentCustomShieldsValues;

    public function __construct(CurrentValues $currentValues)
    {
        $this->currentValues = $currentValues;
    }

    /**
     * @return array|string[][]
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    public function getCurrentCustomMeleeWeaponsValues(): array
    {
        if ($this->currentCustomMeleeWeaponsValues !== null) {
            return $this->currentCustomMeleeWeaponsValues;
        }
        $this->currentCustomMeleeWeaponsValues = $this->assembleCurrentCustomMeleeWeaponsValues();

        return $this->currentCustomMeleeWeaponsValues;
    }

    /**
     * @return array
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    private function assembleCurrentCustomMeleeWeaponsValues(): array
    {
        return $this->assembleCurrentCustomArmamentsValues(
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

    /**
     * @param array $customArmamentKeys
     * @param string $customArmamentNameKey
     * @param string $customArmamentTwoHandedOnlyKey
     * @return array
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    private function assembleCurrentCustomArmamentsValues(
        array $customArmamentKeys,
        string $customArmamentNameKey,
        string $customArmamentTwoHandedOnlyKey
    ): array
    {
        $nameIndexedValues = [];
        $armamentNames = (array)$this->currentValues->getCurrentValue($customArmamentNameKey);
        foreach ($armamentNames as $index => $armamentName) {
            $customArmament = [];
            foreach ($customArmamentKeys as $typeName) {
                $sameTypeValues = $typeName === $customArmamentNameKey
                    ? $armamentNames
                    : (array)$this->currentValues->getCurrentValue($typeName);
                if ($typeName === $customArmamentTwoHandedOnlyKey) {
                    $sameTypeValues[$index] = (bool)($sameTypeValues[$index] ?? false);
                } elseif (($sameTypeValues[$index] ?? null) === null) {
                    throw new Exceptions\BrokenNewArmamentValues(
                        "Missing '{$typeName}' on index '{$index}' for a new armament '{$armamentName}'"
                    );
                }
                $customArmament[$typeName] = $sameTypeValues[$index];
            }
            // re-index everything from integer index to armament name
            $nameIndexedValues[$armamentName] = $customArmament;
        }

        return $nameIndexedValues;
    }

    /**
     * @return array|string[][]
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    public function getCurrentCustomRangedWeaponsValues(): array
    {
        if ($this->currentCustomRangedWeaponsValues !== null) {
            return $this->currentCustomRangedWeaponsValues;
        }
        $this->currentCustomRangedWeaponsValues = $this->assembleCurrentCustomRangedWeaponsValues();

        return $this->currentCustomRangedWeaponsValues;
    }

    /**
     * @return array
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    private function assembleCurrentCustomRangedWeaponsValues(): array
    {
        return $this->assembleCurrentCustomArmamentsValues(
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
                self::CUSTOM_RANGED_WEAPON_MAXIMAL_APPLICABLE_STRENGTH,
            ],
            self::CUSTOM_RANGED_WEAPON_NAME,
            self::CUSTOM_RANGED_WEAPON_TWO_HANDED_ONLY
        );
    }

    /**
     * @return array|string[][]
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    public function getCurrentCustomBodyArmorsValues(): array
    {
        if ($this->currentCustomBodyArmorsValues !== null) {
            return $this->currentCustomBodyArmorsValues;
        }
        $this->currentCustomBodyArmorsValues = $this->assembleCurrentCustomBodyArmorsValues();

        return $this->currentCustomBodyArmorsValues;
    }

    /**
     * @return array
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    private function assembleCurrentCustomBodyArmorsValues(): array
    {
        return $this->assembleCurrentCustomArmamentsValues(
            [
                self::CUSTOM_BODY_ARMOR_NAME,
                self::CUSTOM_BODY_ARMOR_REQUIRED_STRENGTH,
                self::CUSTOM_BODY_ARMOR_RESTRICTION,
                self::CUSTOM_BODY_ARMOR_PROTECTION,
                self::CUSTOM_BODY_ARMOR_WEIGHT,
                self::CUSTOM_BODY_ARMOR_ROUNDS_TO_PUT_ON,
            ],
            self::CUSTOM_BODY_ARMOR_NAME,
            ''
        );
    }

    /**
     * @return array|string[][]
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    public function getCurrentCustomHelmsValues(): array
    {
        if ($this->currentCustomHelmsValues !== null) {
            return $this->currentCustomHelmsValues;
        }
        $this->currentCustomHelmsValues = $this->assembleCurrentCustomHelmsValues();

        return $this->currentCustomHelmsValues;
    }

    /**
     * @return array
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    private function assembleCurrentCustomHelmsValues(): array
    {
        return $this->assembleCurrentCustomArmamentsValues(
            [
                self::CUSTOM_HELM_NAME,
                self::CUSTOM_HELM_REQUIRED_STRENGTH,
                self::CUSTOM_HELM_RESTRICTION,
                self::CUSTOM_HELM_PROTECTION,
                self::CUSTOM_HELM_WEIGHT,
            ],
            self::CUSTOM_HELM_NAME,
            ''
        );
    }

    /**
     * @return array|string[][]
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    public function getCurrentCustomShieldsValues(): array
    {
        if ($this->currentCustomShieldsValues !== null) {
            return $this->currentCustomShieldsValues;
        }
        $this->currentCustomShieldsValues = $this->assembleCurrentCustomShieldsValues();

        return $this->currentCustomShieldsValues;
    }

    /**
     * @return array
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    private function assembleCurrentCustomShieldsValues(): array
    {
        return $this->assembleCurrentCustomArmamentsValues(
            [
                self::CUSTOM_SHIELD_NAME,
                self::CUSTOM_SHIELD_COVER,
                self::CUSTOM_SHIELD_RESTRICTION,
                self::CUSTOM_SHIELD_WEIGHT,
                self::CUSTOM_SHIELD_REQUIRED_STRENGTH,
            ],
            self::CUSTOM_SHIELD_NAME,
            self::CUSTOM_SHIELD_TWO_HANDED_ONLY
        );
    }

    public function getMeleeWeaponValue(): ?string
    {
        return $this->currentValues->getCurrentValue(AttackRequest::MELEE_WEAPON);
    }

    public function getMeleeWeaponHoldingValue(): ?string
    {
        return $this->currentValues->getCurrentValue(AttackRequest::MELEE_WEAPON_HOLDING);
    }

    public function getShieldValue(): ?string
    {
        return $this->currentValues->getCurrentValue(AttackRequest::SHIELD);
    }

    public function getShieldHoldingValue(): ?string
    {
        return $this->currentValues->getCurrentValue(AttackRequest::SHIELD_HOLDING);
    }

    public function getRangedWeaponValue(): ?string
    {
        return $this->currentValues->getCurrentValue(AttackRequest::RANGED_WEAPON);
    }

    public function getRangedWeaponHoldingValue(): ?string
    {
        return $this->currentValues->getCurrentValue(AttackRequest::RANGED_WEAPON_HOLDING);
    }

    public function getHelmValue(): ?string
    {
        return $this->currentValues->getCurrentValue(AttackRequest::HELM);
    }

    public function getBodyArmorValue(): ?string
    {
        return $this->currentValues->getCurrentValue(AttackRequest::BODY_ARMOR);
    }

}