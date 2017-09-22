<?php
namespace DrdPlus\Fight;

class CurrentValues extends Values
{
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

    /** @var array */
    private $valuesFromInput;
    /** @var HistoryWithSkillRanks */
    private $historyValues;

    /**
     * @param array $valuesFromInput
     * @param HistoryWithSkillRanks $historyValues
     */
    public function __construct(array $valuesFromInput, HistoryWithSkillRanks $historyValues)
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
     * @return string|string[]|null
     */
    public function getPreviousValue(string $name)
    {
        return $this->historyValues->getHistoryValue($name);
    }

    /**
     * @param string $name
     * @return null|string[]|array|string
     */
    public function getCurrentValue(string $name)
    {
        return $this->valuesFromInput[$name] ?? null;
    }

    private $customMeleeWeaponsValues;

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
        $customMeleeWeaponKeys = [
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
        ];
        $nameIndexedValues = [];
        $weaponNames = (array)$this->getValue(self::CUSTOM_MELEE_WEAPON_NAME);
        foreach ($weaponNames as $index => $weaponName) {
            $meleeWeapon = [];
            foreach ($customMeleeWeaponKeys as $typeName) {
                $sameTypeValues = $typeName === self::CUSTOM_MELEE_WEAPON_NAME
                    ? $weaponNames
                    : (array)$this->getValue($typeName);
                if ($typeName === self::CUSTOM_MELEE_WEAPON_TWO_HANDED_ONLY) {
                    $sameTypeValues[$index] = (bool)($sameTypeValues[$index] ?? false);
                } else {
                    if (($sameTypeValues[$index] ?? null) === null) {
                        throw new Exceptions\BrokenNewMeleeWeaponValues(
                            "Missing '{$typeName}' on index '{$index}' for a new melee weapon '{$weaponName}'"
                        );
                    }
                }
                $meleeWeapon[$typeName] = $sameTypeValues[$index];
            }
            // re-index everything from integer index to weapon name
            $nameIndexedValues[$weaponName] = $meleeWeapon;
        }

        return $nameIndexedValues;
    }
}