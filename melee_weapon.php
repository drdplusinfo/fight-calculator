<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\DistanceUnitCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

/** @var Controller $controller */
$selectedMeleeWeapon = $controller->getSelectedMeleeWeapon();
$selectedMeleeWeaponValue = $selectedMeleeWeapon ? $selectedMeleeWeapon->getValue() : null;
?>
<div class="panel">
    <label>
        <select name="<?= $controller::MELEE_WEAPON ?>" title="Melee weapon">
            <?php foreach ($controller->getMeleeWeaponCodes() as $weaponCategory => $meleeWeaponCategory) {
                ?>
                <optgroup label="<?= WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2) ?>">
                    <?php
                    /** @var string[] $meleeWeaponCategory */
                    foreach ($meleeWeaponCategory as $meleeWeaponValue) {
                        ?>
                        <option value="<?= $meleeWeaponValue ?>"
                                <?php if ($selectedMeleeWeaponValue === $meleeWeaponValue) { ?>selected<?php } ?>
                        >
                            <?= MeleeWeaponCode::getIt($meleeWeaponValue)->translateTo('cs') ?>
                        </option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
    </label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::MAIN_HAND ?>" name="<?= $controller::MELEE_WEAPON_HOLDING ?>"
               <?php if ($controller->getSelectedMeleeWeaponHolding()->getValue() === ItemHoldingCode::MAIN_HAND) { ?>checked<?php } ?>>
        v dominantní ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::OFFHAND ?>" name="<?= $controller::MELEE_WEAPON_HOLDING ?>"
               <?php if ($controller->getSelectedMeleeWeaponHolding()->getValue() === ItemHoldingCode::OFFHAND) { ?>checked<?php } ?>>
        v druhé
        ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::TWO_HANDS ?>" name="<?= $controller::MELEE_WEAPON_HOLDING ?>"
               <?php if ($controller->getSelectedMeleeWeaponHolding()->getValue() === ItemHoldingCode::TWO_HANDS) { ?>checked<?php } ?>>
        obouručně
    </label>
</div>
<div class="panel">
    <div class="panel">
        <label>dovednost <select name="<?= $controller::MELEE_FIGHT_SKILL ?>">
                <?php
                $selectedSkillForMelee = $controller->getSelectedMeleeSkillCode();
                foreach ($controller->getPossibleSkillsForMelee() as $skillCode) {
                    ?>
                    <option value="<?= $skillCode->getValue() ?>"
                            <?php if ($selectedSkillForMelee->getValue() === $skillCode->getValue()) { ?>selected<?php } ?>>
                        <?= $skillCode->translateTo('cs') ?>
                    </option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div class="panel">
        <label>na stupni <input type="radio" value="0" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                                <?php if ($controller->getSelectedMeleeSkillRank() === 0) { ?>checked<?php } ?>> 0,
        </label>
        <label><input type="radio" value="1" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedMeleeSkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedMeleeSkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedMeleeSkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
</div>
<table class="block result">
    <?php $meleeFightProperties = $controller->getMeleeWeaponFightProperties(); ?>
    <tbody>
    <tr>
        <td>BČ <img class="line-sized" src="images/emojione/fight-2694.png"></td>
        <td><?= $meleeFightProperties->getFightNumber() ?></td>
    </tr>
    <tr>
        <td>ÚČ <img class="line-sized" src="images/emojione/fight-number-1f624.png"></td>
        <td><?= $meleeFightProperties->getAttackNumber(
                new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
                Size::getIt(0)
            ) ?></td>
    </tr>
    <tr>
        <td>ZZ <img class="line-sized" src="images/emojione/base-of-wounds-1f480.png"></td>
        <td><?= $meleeFightProperties->getBaseOfWounds() ?></td>
    </tr>
    <tr>
        <td>OČ <img class="line-sized" src="images/emojione/defense-number-1f6e1.png"></td>
        <td><?= $meleeFightProperties->getDefenseNumberWithWeaponlike() ?></td>
    </tr>
    </tbody>
</table>
<div class="block"><input type="submit" value="Přepočítat"></div>
