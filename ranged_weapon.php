<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\DistanceUnitCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

/** @var Controller $controller */
$selectedRangedWeapon = $controller->getSelectedRangedWeapon();
$selectedRangedWeaponValue = $selectedRangedWeapon ? $selectedRangedWeapon->getValue() : null;

?>
<div class="panel">
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
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::MAIN_HAND ?>" name="<?= $controller::RANGED_WEAPON_HOLDING ?>"
               <?php if ($controller->getSelectedRangedWeaponHolding()->getValue() === ItemHoldingCode::MAIN_HAND) { ?>checked<?php } ?>>
        v dominantní ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::OFFHAND ?>" name="<?= $controller::RANGED_WEAPON_HOLDING ?>"
               <?php if ($controller->getSelectedRangedWeaponHolding()->getValue() === ItemHoldingCode::OFFHAND) { ?>checked<?php } ?>>
        v druhé ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::TWO_HANDS ?>" name="<?= $controller::RANGED_WEAPON_HOLDING ?>"
               <?php if ($controller->getSelectedRangedWeaponHolding()->getValue() === ItemHoldingCode::TWO_HANDS) { ?>checked<?php } ?>>
        obouručně
    </label>
</div>
<div class="block skill">
    <div class="panel">
        <label><select name="<?= $controller::RANGED_FIGHT_SKILL ?>">
                <?php foreach ($controller->getPossibleSkillsForRanged() as $skillCode) {
                    ?>
                    <option value="<?= $skillCode->getValue() ?>"><?= $skillCode->translateTo('cs') ?></option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div class="panel">
        <label>na stupni <input type="radio" value="0" name="<?= $controller::RANGED_FIGHT_SKILL_RANK ?>"
                                <?php if ($controller->getSelectedRangedSkillRank() === 0) { ?>checked<?php } ?>> 0,
        </label>
        <label><input type="radio" value="1" name="<?= $controller::RANGED_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedRangedSkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= $controller::RANGED_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedRangedSkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= $controller::RANGED_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedRangedSkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
</div>
<table class="block result with-image">
    <?php $rangedFightProperties = $controller->getRangedFightProperties(); ?>
    <tbody>
    <tr>
        <td>BČ</td>
        <td><img class="line-sized" src="images/emojione/fight-2694.png"></td>
        <td><?= $rangedFightProperties->getFightNumber() ?></td>
    </tr>
    <tr>
        <td>ÚČ</td>
        <td><img class="line-sized" src="images/emojione/fight-number-1f624.png"></td>
        <td><?= $rangedFightProperties->getAttackNumber(
                new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
                Size::getIt(0)
            ) ?></td>
    </tr>
    <tr>
        <td>ZZ</td>
        <td><img class="line-sized" src="images/emojione/base-of-wounds-1f480.png"></td>
        <td><?= $rangedFightProperties->getBaseOfWounds() ?></td>
    </tr>
    <tr>
        <td>OČ</td>
        <td><img class="line-sized" src="images/emojione/defense-number-1f6e1.png"></td>
        <td><?= $rangedFightProperties->getDefenseNumberWithWeaponlike() ?></td>
    </tr>
    </tbody>
</table>
<div class="block"><input type="submit" value="Přepočítat"></div>
