<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Body\PhysicalWoundTypeCode;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Tables;
use Granam\Boolean\Tools\ToBoolean;
use Granam\Integer\PositiveIntegerObject;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;

class CustomArmamentsRegistrar extends StrictObject
{
    /** @var CustomArmamentAdder */
    private $customArmamentAdder;
    /** @var Tables */
    private $tables;

    public function __construct(CustomArmamentAdder $customArmamentAdder, Tables $tables)
    {
        $this->customArmamentAdder = $customArmamentAdder;
        $this->tables = $tables;
    }

    /**
     * @param CurrentArmamentsValues $currentValues
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    public function registerCustomArmaments(CurrentArmamentsValues $currentValues): void
    {
        $this->registerCustomMeleeWeapons($currentValues);
        $this->registerCustomRangedWeapons($currentValues);
        $this->registerCustomBodyArmors($currentValues);
        $this->registerCustomHelms($currentValues);
    }

    /**
     * @param CurrentArmamentsValues $currentValues
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    protected function registerCustomMeleeWeapons(CurrentArmamentsValues $currentValues): void
    {
        foreach ($currentValues->getCurrentCustomMeleeWeaponsValues() as $customMeleeWeaponsValue) {
            $this->customArmamentAdder->addCustomMeleeWeapon(
                $customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_NAME],
                WeaponCategoryCode::getIt($customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_CATEGORY]),
                Strength::getIt($customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_REQUIRED_STRENGTH]),
                ToInteger::toInteger($customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_OFFENSIVENESS]),
                ToInteger::toInteger($customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_LENGTH]),
                ToInteger::toInteger($customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_WOUNDS]),
                PhysicalWoundTypeCode::getIt($customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_WOUND_TYPE]),
                ToInteger::toInteger($customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_COVER]),
                new Weight(
                    $customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_WEIGHT],
                    Weight::KG,
                    $this->tables->getWeightTable()
                ),
                ToBoolean::toBoolean($customMeleeWeaponsValue[CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_TWO_HANDED_ONLY])
            );
        }
    }

    /**
     * @param CurrentArmamentsValues $currentValues
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    protected function registerCustomRangedWeapons(CurrentArmamentsValues $currentValues): void
    {
        foreach ($currentValues->getCurrentCustomRangedWeaponsValues() as $customRangedWeaponsValue) {
            $this->customArmamentAdder->addCustomRangedWeapon(
                $customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_NAME],
                WeaponCategoryCode::getIt($customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_CATEGORY]),
                Strength::getIt($customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_REQUIRED_STRENGTH]),
                ToInteger::toInteger($customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_OFFENSIVENESS]),
                (new Distance(
                    $customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_RANGE_IN_M],
                    Distance::METER,
                    $this->tables->getDistanceTable()
                ))->getBonus(),
                ToInteger::toInteger($customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_WOUNDS]),
                PhysicalWoundTypeCode::getIt($customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_WOUND_TYPE]),
                ToInteger::toInteger($customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_COVER]),
                new Weight(
                    $customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_WEIGHT],
                    Weight::KG,
                    $this->tables->getWeightTable()
                ),
                ToBoolean::toBoolean($customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_TWO_HANDED_ONLY]),
                Strength::getIt($customRangedWeaponsValue[CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_MAXIMAL_APPLICABLE_STRENGTH])
            );
        }
    }

    /**
     * @param CurrentArmamentsValues $currentValues
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    protected function registerCustomBodyArmors(CurrentArmamentsValues $currentValues): void
    {
        foreach ($currentValues->getCurrentCustomBodyArmorsValues() as $customBodyArmorsValue) {
            $this->customArmamentAdder->addCustomBodyArmor(
                $customBodyArmorsValue[CurrentArmamentsValues::CUSTOM_BODY_ARMOR_NAME],
                Strength::getIt($customBodyArmorsValue[CurrentArmamentsValues::CUSTOM_BODY_ARMOR_REQUIRED_STRENGTH]),
                ToInteger::toInteger($customBodyArmorsValue[CurrentArmamentsValues::CUSTOM_BODY_ARMOR_RESTRICTION]),
                ToInteger::toInteger($customBodyArmorsValue[CurrentArmamentsValues::CUSTOM_BODY_ARMOR_PROTECTION]),
                new Weight(
                    $customBodyArmorsValue[CurrentArmamentsValues::CUSTOM_BODY_ARMOR_WEIGHT],
                    Weight::KG,
                    $this->tables->getWeightTable()
                ),
                new PositiveIntegerObject($customBodyArmorsValue[CurrentArmamentsValues::CUSTOM_BODY_ARMOR_ROUNDS_TO_PUT_ON])
            );
        }
    }

    /**
     * @param CurrentArmamentsValues $currentValues
     * @throws \DrdPlus\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    protected function registerCustomHelms(CurrentArmamentsValues $currentValues): void
    {
        foreach ($currentValues->getCurrentCustomHelmsValues() as $customHelmsValue) {
            $this->customArmamentAdder->addCustomHelm(
                $customHelmsValue[CurrentArmamentsValues::CUSTOM_HELM_NAME],
                Strength::getIt($customHelmsValue[CurrentArmamentsValues::CUSTOM_HELM_REQUIRED_STRENGTH]),
                ToInteger::toInteger($customHelmsValue[CurrentArmamentsValues::CUSTOM_HELM_RESTRICTION]),
                ToInteger::toInteger($customHelmsValue[CurrentArmamentsValues::CUSTOM_HELM_PROTECTION]),
                new Weight(
                    $customHelmsValue[CurrentArmamentsValues::CUSTOM_HELM_WEIGHT],
                    Weight::KG,
                    $this->tables->getWeightTable()
                )
            );
        }
    }

}