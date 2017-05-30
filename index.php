<?php
namespace DrdPlus\Fight;

include_once __DIR__ . '/vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', '1');

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\DistanceUnitCode;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

$controller = new Controller();
$selectedMeleeWeapon = $controller->getSelectedMeleeWeapon();
$selectedMeleeWeaponValue = $selectedMeleeWeapon ? $selectedMeleeWeapon->getValue() : null;
$selectedRangedWeapon = $controller->getSelectedRangedWeapon();
$selectedRangedWeaponValue = $selectedRangedWeapon ? $selectedRangedWeapon->getValue() : null;
?>
<html>
<body>
<form action="" method="get">
    <select name="meleeWeapon" title="Melee weapon">
        <?php foreach ($controller->getMeleeWeaponCodes() as $weaponCategory => $meleeWeaponCategory) {
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
        <?php foreach ($controller->getRangedWeaponCodes() as $weaponCategory => $rangedWeaponCategory) {
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
<div>
    <?php $meleeFightProperties = $controller->getMeleeFightProperties(); ?>
    <div>Boj: <?= $meleeFightProperties->getFight() ?></div>
    <div>Bojové číslo: <?= $meleeFightProperties->getFightNumber() ?></div>
    <div>Útok: <?= $meleeFightProperties->getAttack() ?></div>
    <div>
        ÚČ: <?= $meleeFightProperties->getAttackNumber(new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()), Size::getIt(0)) ?>
    </div>
    <div>
        ZZ: <?= $meleeFightProperties->getBaseOfWounds() ?>
    </div>
    <div>
        Obrana: <?= $meleeFightProperties->getDefense() ?>
    </div>
    <div>
        Obranné číslo: <?= $meleeFightProperties->getDefenseNumber() ?>
    </div>
    <div>
        Obranné číslo se zbraní: <?= $meleeFightProperties->getDefenseNumberWithWeaponlike() ?>
    </div>
    <div>
        Obranné číslo se štítem: <?= $meleeFightProperties->getDefenseNumberWithShield() ?>
    </div>
</div>
</body>
</html>
