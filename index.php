<?php
namespace DrdPlus\Fight;

include_once __DIR__ . '/vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', '1');

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;

$controller = new Controller();
$selectedMeleeWeapon = $controller->getSelectedMeleeWeapon();
$selectedMeleeWeaponValue = $selectedMeleeWeapon ? $selectedMeleeWeapon->getValue() : null;
$selectedRangedWeapon = $controller->getSelectedRangedWeapon();
$selectedRangedWeaponValue = $selectedRangedWeapon ? $selectedRangedWeapon->getValue() : null;

$meleeWeapons = [
    WeaponCategoryCode::AXE => MeleeWeaponCode::getAxeCodes(),
    WeaponCategoryCode::KNIFE_AND_DAGGER => MeleeWeaponCode::getKnifeAndDaggerCodes(),
    WeaponCategoryCode::MACE_AND_CLUB => MeleeWeaponCode::getMaceAndClubCodes(),
    WeaponCategoryCode::MORNINGSTAR_AND_MORGENSTERN => MeleeWeaponCode::getMorningstarAndMorgensternCodes(),
    WeaponCategoryCode::SABER_AND_BOWIE_KNIFE => MeleeWeaponCode::getSaberAndBowieKnifeCodes(),
    WeaponCategoryCode::STAFF_AND_SPEAR => MeleeWeaponCode::getStaffAndSpearCodes(),
    WeaponCategoryCode::SWORD => MeleeWeaponCode::getSwordCodes(),
    WeaponCategoryCode::VOULGE_AND_TRIDENT => MeleeWeaponCode::getVoulgeAndTridentCodes(),
    WeaponCategoryCode::UNARMED => MeleeWeaponCode::getUnarmedCodes(),
];
$rangedWeapons = [
    WeaponCategoryCode::BOW => RangedWeaponCode::getBowValues(),
    WeaponCategoryCode::CROSSBOW => RangedWeaponCode::getCrossbowValues(),
];
?>
<html>
<body>
<form action="" method="get">
    <select name="meleeWeapon" title="Melee weapon">
        <?php foreach ($meleeWeapons as $weaponCategory => $meleeWeaponCategory) {
            ?>
            <optgroup label="<?= WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2) ?>">
                <?php
                /** @var string[] $meleeWeaponCategory */
                foreach ($meleeWeaponCategory as $meleeWeapon) {
                    ?>
                    <option value="<?= $meleeWeapon ?>"
                            <?php if ($selectedMeleeWeaponValue && $selectedMeleeWeaponValue === $meleeWeapon) { ?>selected<?php } ?>
                    >
                        <?= MeleeWeaponCode::getIt($meleeWeapon)->translateTo('cs') ?>
                    </option>
                <?php } ?>
            </optgroup>
        <?php } ?>
    </select>
    <select name="rangedWeapon" title="Ranged weapon">
        <?php foreach ($rangedWeapons as $weaponCategory => $rangedWeaponCategory) {
            ?>
            <optgroup label="<?= WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2) ?>">
                <?php
                /** @var string[] $rangedWeaponCategory */
                foreach ($rangedWeaponCategory as $rangedWeapon) {
                    ?>
                    <option value="<?= $rangedWeapon ?>"
                            <?php if ($selectedRangedWeaponValue && $selectedRangedWeaponValue === $rangedWeapon) { ?>selected<?php } ?>
                    >
                        <?= RangedWeaponCode::getIt($rangedWeapon)->translateTo('cs') ?>
                    </option>
                <?php } ?>
            </optgroup>
        <?php } ?>
    </select>
    <input type="submit" value="OK">
</form>
</body>
</html>
