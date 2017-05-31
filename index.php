<?php
namespace DrdPlus\Fight;

include_once __DIR__ . '/vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', '1');

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\DistanceUnitCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\ProfessionCode;
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
<head>
    <link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="" method="post">
    <input type="hidden" name="deleteHistory" value="1">
    <input type="submit" value="Vymazat historii">
</form>
<form class="block" action="" method="get">
    <div class="panel">
        <h2>Na blízko</h2>
        <select name="<?= $controller::MELEE_WEAPON ?>" title="Melee weapon">
            <?php foreach ($controller->getMeleeWeaponCodes() as $weaponCategory => $meleeWeaponCategory) {
                ?>
                <optgroup label="<?= WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2) ?>">
                    <?php
                    /** @var string[] $meleeWeaponCategory */
                    foreach ($meleeWeaponCategory as $meleeWeaponValue) {
                        ?>
                        <option value="<?= $meleeWeaponValue ?>"
                                <?php if ($selectedMeleeWeaponValue && $selectedMeleeWeaponValue === $meleeWeaponValue) { ?>selected<?php } ?>
                        >
                            <?= MeleeWeaponCode::getIt($meleeWeaponValue)->translateTo('cs') ?>
                        </option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
        <label>
            <input type="radio" value="<?= ItemHoldingCode::MAIN_HAND ?>" name="<?= $controller::MELEE_HOLDING ?>"
                   <?php if ($controller->getSelectedMeleeHolding()->getValue() === ItemHoldingCode::MAIN_HAND) { ?>checked<?php } ?>>
            v dominantní ruce</label>
        <label>
            <input type="radio" value="<?= ItemHoldingCode::OFFHAND ?>" name="<?= $controller::MELEE_HOLDING ?>"
                   <?php if ($controller->getSelectedMeleeHolding()->getValue() === ItemHoldingCode::OFFHAND) { ?>checked<?php } ?>>
            v druhé
            ruce</label>
        <label>
            <input type="radio" value="<?= ItemHoldingCode::TWO_HANDS ?>" name="<?= $controller::MELEE_HOLDING ?>"
                   <?php if ($controller->getSelectedMeleeHolding()->getValue() === ItemHoldingCode::TWO_HANDS) { ?>checked<?php } ?>>
            obouručně
        </label>
        <div>
            <label><select name="<?= $controller::MELEE_FIGHT_SKILL ?>">
                    <?php
                    $selectedSkillForMelee = $controller->getSelectedMeleeSkillCode();
                    foreach ($controller->getPossibleSkillsForMelee() as $skillCode) {
                        ?>
                        <option value="<?= $skillCode->getValue() ?>"
                                <?php if ($selectedSkillForMelee->getValue() === $skillCode->getValue()) { ?>selected<?php } ?>>
                            <?= $skillCode->translateTo('cs') ?>
                        </option>
                    <?php } ?>
                </select></label>
            <label>Stupeň dovednosti <input type="number" min="0" max="3"
                                            value="<?= $controller->getSelectedMeleeSkillRankValue() ?>"
                                            name="<?= $controller::MELEE_FIGHT_SKILL_VALUE ?>"></label>
        </div>
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
    </div>
    <div class="panel">
        <h2>Na dálku</h2>
        <select name="<?= $controller::RANGED_WEAPON ?>" title="Ranged weapon">
            <?php foreach ($controller->getRangedWeaponCodes() as $weaponCategory => $rangedWeaponCategory) {
                ?>
                <optgroup label="<?= WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2) ?>">
                    <?php
                    /** @var string[] $rangedWeaponCategory */
                    foreach ($rangedWeaponCategory as $rangedWeaponValue) {
                        ?>
                        <option value="<?= $rangedWeaponValue ?>"
                                <?php if ($selectedRangedWeaponValue && $selectedRangedWeaponValue === $rangedWeaponValue) { ?>selected<?php } ?>
                        >
                            <?= RangedWeaponCode::getIt($rangedWeaponValue)->translateTo('cs') ?>
                        </option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
        <label>
            <input type="radio" value="<?= ItemHoldingCode::MAIN_HAND ?>" name="<?= $controller::RANGED_HOLDING ?>"
                   <?php if ($controller->getSelectedRangedHolding()->getValue() === ItemHoldingCode::MAIN_HAND) { ?>checked<?php } ?>>
            v dominantní ruce</label>
        <label>
            <input type="radio" value="<?= ItemHoldingCode::OFFHAND ?>" name="<?= $controller::RANGED_HOLDING ?>"
                   <?php if ($controller->getSelectedRangedHolding()->getValue() === ItemHoldingCode::OFFHAND) { ?>checked<?php } ?>>
            v druhé ruce</label>
        <label>
            <input type="radio" value="<?= ItemHoldingCode::TWO_HANDS ?>" name="<?= $controller::RANGED_HOLDING ?>"
                   <?php if ($controller->getSelectedRangedHolding()->getValue() === ItemHoldingCode::TWO_HANDS) { ?>checked<?php } ?>>
            obouručně
        </label>
        <div>
            <label><select name="<?= $controller::RANGED_FIGHT_SKILL ?>">
                    <?php foreach ($controller->getPossibleSkillsForRanged() as $skillCode) {
                        ?>
                        <option value="<?= $skillCode->getValue() ?>"><?= $skillCode->translateTo('cs') ?></option>
                    <?php } ?>
                </select></label>
            <label>Stupeň dovednosti <input type="number" min="0" max="3"
                                            value="<?= $controller->getSelectedRangedSkillRankValue() ?>"
                                            name="<?= $controller::RANGED_FIGHT_SKILL_VALUE ?>"></label>
        </div>
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
    </div>
    <div class="panel">
        <label>Štít <select
                    name="<?= $controller::SHIELD ?>"><?php foreach ($controller->getPossibleShields() as $shield) { ?>
                    <option value="<?= $shield->getValue() ?>"
                            <?php if ($controller->getSelectedShield()->getValue() === $shield->getValue()){ ?>selected<?php } ?>>
                        <?= $shield->translateTo('cs') ?>
                    </option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div class="panel">
        <label>
            Stupeň dovednosti <?= $controller->getPossibleSkillForShield()->translateTo('cs') ?> <input
                    type="number" min="0" max="3"
                    name="<?= $controller::SHIELD_SKILL_VALUE ?>"
                    value="<?= $controller->getSelectedShieldSkillRank() ?>">
        </label>
    </div>
    <div class="panel">
        <label>Zbroj <select
                    name="<?= $controller::BODY_ARMOR ?>"><?php foreach ($controller->getPossibleBodyArmors() as $bodyArmor) {
                    ?>
                <option value="<?= $bodyArmor->getValue() ?>"
                        <?php if ($controller->getSelectedBodyArmor()->getValue() === $bodyArmor->getValue()){ ?>selected<?php } ?>>
                    <?= $bodyArmor->translateTo('cs') ?></option><?php
                } ?>
            </select>
        </label>
    </div>
    <div class="panel">
        <label>Helma <select
                    name="<?= $controller::HELM ?>"><?php foreach ($controller->getPossibleHelms() as $helm) { ?>
                    <option value="<?= $helm->getValue() ?>"
                            <?php if ($controller->getSelectedHelm()->getValue() === $helm->getValue()){ ?>selected<?php } ?>>
                        <?= $helm->translateTo('cs') ?></option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div class="panel">
        <label>
            Stupeň dovednosti <?= $controller->getPossibleSkillForArmor()->translateTo('cs') ?> <input
                    type="number" min="0" max="3"
                    name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                    value="<?= $controller->getSelectedArmorSkillRank() ?>">
        </label>
    </div>
    <div class="panel">
        <h3>Vlastnosti</h3>
        <div>
            <label>Povolání <select name="<?= $controller::PROFESSION ?>">
                    <?php foreach (ProfessionCode::getPossibleValues() as $professionValue) {
                        ?>
                        <option value="<?= $professionValue ?>"
                                <?php if ($controller->getSelectedProfessionCode()->getValue() === $professionValue) { ?>selected<?php } ?>>
                            <?= ProfessionCode::getIt($professionValue)->translateTo('cs') ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
            <label>Síla <input type="number" name="<?= $controller::STRENGTH ?>" min="-40" max="40"
                               value="<?= $controller->getSelectedStrength()->getValue() ?>"></label>
            <label>Obratnost <input type="number" name="<?= $controller::AGILITY ?>" min="-40" max="40"
                                    value="<?= $controller->getSelectedAgility()->getValue() ?>"></label>
            <label>Zručnost <input type="number" name="<?= $controller::KNACK ?>" min="-40" max="40"
                                   value="<?= $controller->getSelectedKnack()->getValue() ?>"></label>
            <label>Vůle <input type="number" name="<?= $controller::WILL ?>" min="-40" max="40"
                               value="<?= $controller->getSelectedWill()->getValue() ?>"></label>
            <label>Inteligence <input type="number" name="<?= $controller::INTELLIGENCE ?>" min="-40" max="40"
                                      value="<?= $controller->getSelectedIntelligence()->getValue() ?>"></label>
            <label>Charisma <input type="number" name="<?= $controller::CHARISMA ?>" min="-40" max="40"
                                   value="<?= $controller->getSelectedCharisma()->getValue() ?>"></label>
            <label>Výška v cm <input type="number" name="<?= $controller::HEIGHT_IN_CM ?>" min="110" max="290"
                                     value="<?= $controller->getSelectedHeightInCm()->getValue() ?>"></label>
            <label>Velikost <input type="number" name="<?= $controller::SIZE ?>" min="-10" max="10"
                                   value="<?= $controller->getSelectedSize()->getValue() ?>"></label>
            <input type="submit" value="OK"></div>
    </div>
</form>
</body>
</html>
