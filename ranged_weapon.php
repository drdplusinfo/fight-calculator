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
        <input type="radio" value="<?= ItemHoldingCode::MAIN_HAND ?>" name="<?= $controller::RANGED_HOLDING ?>"
               <?php if ($controller->getSelectedRangedHolding()->getValue() === ItemHoldingCode::MAIN_HAND) { ?>checked<?php } ?>>
        v dominantní ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::OFFHAND ?>" name="<?= $controller::RANGED_HOLDING ?>"
               <?php if ($controller->getSelectedRangedHolding()->getValue() === ItemHoldingCode::OFFHAND) { ?>checked<?php } ?>>
        v druhé ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::TWO_HANDS ?>" name="<?= $controller::RANGED_HOLDING ?>"
               <?php if ($controller->getSelectedRangedHolding()->getValue() === ItemHoldingCode::TWO_HANDS) { ?>checked<?php } ?>>
        obouručně
    </label>
</div>
<div class="panel">
    <label>dovednost <select name="<?= $controller::RANGED_FIGHT_SKILL ?>">
            <?php foreach ($controller->getPossibleSkillsForRanged() as $skillCode) {
                ?>
                <option value="<?= $skillCode->getValue() ?>"><?= $skillCode->translateTo('cs') ?></option>
            <?php } ?>
        </select>
    </label>
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
<div><input type="submit" value="OK"></div>
<div class="panel">
    <?php $rangedFightProperties = $controller->getRangedFightProperties(); ?>
    <div>Bojové číslo <span class="hint">se zbraní na dálku</span>: <?= $rangedFightProperties->getFightNumber() ?>
    </div>
    <div>
        ÚČ <span class="hint">se zbraní na dálku</span>: <?= $rangedFightProperties->getAttackNumber(
            new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
            Size::getIt(0)
        ) ?>
    </div>
    <div>
        ZZ <span class="hint">se zbraní na dálku</span>: <?= $rangedFightProperties->getBaseOfWounds() ?>
    </div>
    <div>
        Obranné číslo <span
                class="hint">se zbraní na dálku</span>: <?= $rangedFightProperties->getDefenseNumberWithShield() ?>
    </div>
</div>