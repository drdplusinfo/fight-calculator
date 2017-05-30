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
    <h2>Na blízko</h2>
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
    <input type="submit" value="OK">
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
    <h2>Na dálku</h2>
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
    <div>
        <?php $rangedFightProperties = $controller->getRangedFightProperties(); ?>
        <div>Boj: <?= $rangedFightProperties->getFight() ?></div>
        <div>Bojové číslo: <?= $rangedFightProperties->getFightNumber() ?></div>
        <div>Útok: <?= $rangedFightProperties->getAttack() ?></div>
        <div>
            ÚČ: <?= $rangedFightProperties->getAttackNumber(new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()), Size::getIt(0)) ?>
        </div>
        <div>
            ZZ: <?= $rangedFightProperties->getBaseOfWounds() ?>
        </div>
        <div>
            Obrana: <?= $rangedFightProperties->getDefense() ?>
        </div>
        <div>
            Obranné číslo: <?= $rangedFightProperties->getDefenseNumber() ?>
        </div>
        <div>
            Obranné číslo se zbraní: <?= $rangedFightProperties->getDefenseNumberWithWeaponlike() ?>
        </div>
        <div>
            Obranné číslo se štítem: <?= $rangedFightProperties->getDefenseNumberWithShield() ?>
        </div>
    </div>
    <h3>Vlastnosti</h3>
    <label>Síla <input type="number" name="strength" min="-40" max="40"
                       value="<?= $controller->getSelectedStrength()->getValue() ?>"></label>
    <label>Obratnost <input type="number" name="agility" min="-40" max="40"
                            value="<?= $controller->getSelectedAgility()->getValue() ?>"></label>
    <label>Zručnost <input type="number" name="knack" min="-40" max="40"
                           value="<?= $controller->getSelectedKnack()->getValue() ?>"></label>
    <label>Vůle <input type="number" name="will" min="-40" max="40"
                       value="<?= $controller->getSelectedWill()->getValue() ?>"></label>
    <label>Inteligence <input type="number" name="intelligence" min="-40" max="40"
                              value="<?= $controller->getSelectedIntelligence()->getValue() ?>"></label>
    <label>Charisma <input type="number" name="charisma" min="-40" max="40"
                           value="<?= $controller->getSelectedCharisma()->getValue() ?>"></label>
    <label>Výška v cm <input type="number" name="height-in-cm" min="110" max="290"
                             value="<?= $controller->getSelectedHeightInCm()->getValue() ?>"></label>
    <label>Velikost <input type="number" name="size" min="-10" max="10"
                           value="<?= $controller->getSelectedSize()->getValue() ?>"></label>
    <input type="submit" value="OK">
</form>
</body>
</html>
