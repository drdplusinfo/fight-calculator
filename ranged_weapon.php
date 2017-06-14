<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\ItemHoldingCode;

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
<div class="block skill with-skill-ranks">
    <div class="panel">
        <label><select name="<?= $controller::RANGED_FIGHT_SKILL ?>">
                <?php foreach ($controller->getPossibleSkillsForRanged() as $skillCode) {
                    ?>
                    <option value="<?= $skillCode->getValue() ?>"><?= $skillCode->translateTo('cs') ?></option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div class="panel skill-ranks"
         data-history-skill-ranks="<?= htmlspecialchars($controller->getHistoryRangedSkillRanksJson()) ?>">
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
<table class="block result">
    <?php
    /** @noinspection PhpUnusedLocalVariableInspection */
    $fightProperties = $controller->getRangedFightProperties();
    /** @noinspection PhpUnusedLocalVariableInspection */
    $previousFightProperties = $controller->getPreviousRangedFightProperties();
    ?>
    <tbody>
    <?php include __DIR__ . '/fight_properties_trait.php'; ?>
    </tbody>
</table>
<div class="block"><input type="submit" value="Přepočítat"></div>
