<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\ItemHoldingCode;

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
                                <?php if ($selectedMeleeWeaponValue === $meleeWeaponValue) { ?>selected<?php } ?>>
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
<div class="block skill with-skill-ranks">
    <div class="panel">
        <label>
            <select name="<?= $controller::MELEE_FIGHT_SKILL ?>">
                <?php
                $selectedSkillForMelee = $controller->getSelectedMeleeSkillCode();
                foreach ($controller->getPossibleSkillsForMelee() as $skillCode) { ?>
                    <option value="<?= $skillCode->getValue() ?>"
                            <?php if ($selectedSkillForMelee->getValue() === $skillCode->getValue()) { ?>selected<?php } ?>>
                        <?= $skillCode->translateTo('cs') ?>
                    </option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div class="panel skill-ranks"
         data-history-skill-ranks="<?= htmlspecialchars($controller->getHistoryMeleeSkillRanksJson()) ?>">
        <label>na stupni <input type="radio" value="0" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                                <?php if ($controller->getSelectedMeleeSkillRank() === 0) { ?>checked<?php } ?>>0,
        </label>
        <label><input type="radio" value="1" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedMeleeSkillRank() === 1) { ?>checked<?php } ?>>1,
        </label>
        <label><input type="radio" value="2" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedMeleeSkillRank() === 2) { ?>checked<?php } ?>>2,
        </label>
        <label><input type="radio" value="3" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedMeleeSkillRank() === 3) { ?>checked<?php } ?>>3
        </label>
    </div>
</div>
<table class="block result">
    <?php
    /** @noinspection PhpUnusedLocalVariableInspection */
    $fightProperties = $controller->getMeleeWeaponFightProperties();
    /** @noinspection PhpUnusedLocalVariableInspection */
    $previousFightProperties = $controller->getPreviousMeleeWeaponFightProperties();
    ?>
    <tbody>
    <?php include __DIR__ . '/fight_properties_trait.php'; ?>
    </tbody>
</table>
<div class="block"><input type="submit" value="Přepočítat"></div>
