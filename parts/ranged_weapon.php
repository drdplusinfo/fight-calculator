<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Tables\Tables;

/** @var Controller $controller */
/** @var Tables $tables */
$selectedRangedWeapon = $controller->getFight()->getSelectedRangedWeapon();
$selectedRangedWeaponValue = $selectedRangedWeapon ? $selectedRangedWeapon->getValue() : null;

?>
<div class="panel">
    <label class="block">
        <select name="<?= Controller::RANGED_WEAPON ?>" title="Ranged weapon">
            <?php /** @var string[] $rangedWeaponsFromCategory */
            foreach ($controller->getRangedWeapons() as $weaponCategory => $rangedWeaponsFromCategory) {
                ?>
                <optgroup label="<?= WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2) ?>">
                    <?php /** @var array $rangedWeapon */
                    foreach ($rangedWeaponsFromCategory as $rangedWeapon) {
                        /** @var RangedWeaponCode $rangedWeaponCode */
                        $rangedWeaponCode = $rangedWeapon['code']; ?>
                        <option value="<?= $rangedWeaponCode->getValue() ?>"
                                <?php if ($selectedRangedWeaponValue && $selectedRangedWeaponValue === $rangedWeaponCode->getValue()) { ?>selected<?php }
                                if (!$rangedWeapon['canUseIt']) { ?>disabled<?php } ?>>
                            <?= $rangedWeaponCode->translateTo('cs') ?>
                        </option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
    </label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::MAIN_HAND ?>" name="<?= Controller::RANGED_WEAPON_HOLDING ?>"
               <?php if ($controller->getFight()->getSelectedRangedWeaponHolding()->getValue() === ItemHoldingCode::MAIN_HAND) { ?>checked<?php } ?>>
        v dominantní ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::OFFHAND ?>" name="<?= Controller::RANGED_WEAPON_HOLDING ?>"
               <?php if ($controller->getFight()->getSelectedRangedWeaponHolding()->getValue() === ItemHoldingCode::OFFHAND) { ?>checked<?php } ?>>
        v druhé ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::TWO_HANDS ?>" name="<?= Controller::RANGED_WEAPON_HOLDING ?>"
               <?php if ($controller->getFight()->getSelectedRangedWeaponHolding()->getValue() === ItemHoldingCode::TWO_HANDS) { ?>checked<?php } ?>>
        obouručně
    </label>
</div>
<div class="block info-messages">
    <?php foreach ($controller->getMessagesAboutRanged() as $messageAboutRanged) { ?>
        <div class="info-message"><?= $messageAboutRanged ?></div>
    <?php } ?>
</div>
<div class="block skill with-skill-ranks">
    <div class="panel">
        <label><select name="<?= Controller::RANGED_FIGHT_SKILL ?>">
                <?php
                $selectedSkillForRanged = $controller->getFight()->getSelectedRangedSkillCode();
                foreach ($controller->getFight()->getSkillsForRanged() as $skillCode) {
                    ?>
                    <option value="<?= $skillCode->getValue() ?>"
                            <?php if ($selectedSkillForRanged->getValue() === $skillCode->getValue()) { ?>selected<?php } ?>>
                        <?= $skillCode->translateTo('cs') ?>
                    </option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div class="panel skill-ranks"
         data-history-skill-ranks="<?= htmlspecialchars($controller->getHistoryRangedSkillRanksJson()) ?>">
        <label>na stupni <input type="radio" value="0" name="<?= Controller::RANGED_FIGHT_SKILL_RANK ?>"
                                <?php if ($controller->getFight()->getSelectedRangedSkillRank() === 0) { ?>checked<?php } ?>> 0,
        </label>
        <label><input type="radio" value="1" name="<?= Controller::RANGED_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedRangedSkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= Controller::RANGED_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedRangedSkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= Controller::RANGED_FIGHT_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedRangedSkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
    <div class="block">
        <label>vzdálenost cíle <span class="hint">v metrech</span>
            <input type="number" name="<?= Controller::RANGED_TARGET_DISTANCE ?>" min="1" max="900" step="0.1"
                   value="<?= $controller->getFight()->getCurrentTargetDistance()->getMeters() ?>">
        </label>
        <label>velikost cíle <span class="hint">(Vel)</span>
            <input type="number" name="<?= Controller::RANGED_TARGET_SIZE ?>"
                   value="<?= $controller->getFight()->getCurrentTargetSize() ?>">
        </label>
    </div>
</div>
<table class="block result">
    <?php
    /** @noinspection PhpUnusedLocalVariableInspection */
    $fightProperties = $controller->getFight()->getCurrentRangedFightProperties();
    /** @noinspection PhpUnusedLocalVariableInspection */
    $previousFightProperties = $controller->getFight()->getPreviousRangedFightProperties();
    ?>
    <tbody>
    <?php include __DIR__ . '/fight_properties_trait.php'; ?>
    <tr>
        <td>Soubojový dostřel</td>
        <td><img class="line-sized" src="images/emojione/bow-and-arrow-1f3f9.png"></td>
        <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getEncounterRange(), $fightProperties->getEncounterRange()) ?>">
            <?= "{$fightProperties->getEncounterRange()} ({$fightProperties->getEncounterRange()->getInMeters(Tables::getIt())} m)" ?>
        </td>
    </tr>
    <tr>
        <td>Maximální dostřel</td>
        <td><img class="line-sized" src="images/emojione/bow-and-arrow-1f3f9.png"></td>
        <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getMaximalRange(), $fightProperties->getMaximalRange()) ?>">
            <?= "{$fightProperties->getMaximalRange()} ({$fightProperties->getMaximalRange()->getInMeters(Tables::getIt())} m)" ?>
        </td>
    </tr>
    </tbody>
</table>
<div class="block"><input type="submit" value="Přepočítat"></div>
