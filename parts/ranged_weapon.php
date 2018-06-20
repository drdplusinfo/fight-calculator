<?php
namespace DrdPlus\FightCalculator;

use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Tables\Tables;

/** @var FightController $controller */
/** @var Tables $tables */
$selectedRangedWeapon = $controller->getFight()->getCurrentRangedWeapon();
$selectedRangedWeaponValue = $selectedRangedWeapon ? $selectedRangedWeapon->getValue() : null;
?>
<div class="row">
  <h2 id="Na dálku" class="col"><a href="#Na dálku" class="inner">Na dálku</a></h2>
</div>
<fieldset>
    <?php if ($controller->isAddingNewRangedWeapon()) {
        echo $controller->getAddCustomRangedWeaponContent();
    }
    foreach ($controller->getCurrentValues()->getCustomRangedWeaponsValues() as $weaponName => $weaponValues) {
        /** @var array|string[] $weaponValues */
        foreach ($weaponValues as $typeName => $weaponValue) { ?>
          <input type="hidden" name="<?= $typeName ?>[<?= $weaponName ?>]" value="<?= $weaponValue ?>">
        <?php }
    } ?>
  <div class="row <?php if ($controller->isAddingNewRangedWeapon()) { ?>hidden<?php } ?>" id="chooseRangedWeapon">
    <div class="col">
      <a title="Přidat vlastní zbraň na dálku"
         href="<?= $controller->getLocalUrlWithQuery([FightController::ACTION => FightController::ADD_NEW_RANGED_WEAPON]) ?>"
         class="button add">+</a>
      <label>
        <select name="<?= FightController::RANGED_WEAPON ?>" title="Ranged weapon">
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
                        <?= (!$rangedWeapon['canUseIt'] ? '💪 ' : '') . $rangedWeaponCode->translateTo('cs') ?>
                    </option>
                  <?php } ?>
              </optgroup>
            <?php } ?>
        </select>
      </label>
        <?php foreach ($controller->getMessagesAboutRangedWeapons() as $messagesAboutRangedWeapon) { ?>
          <span class="info-message"><?= $messagesAboutRangedWeapon ?></span>
        <?php } ?>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <label>
        <input type="radio" value="<?= ItemHoldingCode::MAIN_HAND ?>"
               name="<?= FightController::RANGED_WEAPON_HOLDING ?>"
               <?php if ($controller->getFight()->getCurrentRangedWeaponHolding()->getValue() === ItemHoldingCode::MAIN_HAND) { ?>checked<?php } ?>>
        v dominantní ruce</label>
      <label>
        <input type="radio" value="<?= ItemHoldingCode::OFFHAND ?>" name="<?= FightController::RANGED_WEAPON_HOLDING ?>"
               <?php if ($controller->getFight()->getCurrentRangedWeaponHolding()->getValue() === ItemHoldingCode::OFFHAND) { ?>checked<?php } ?>>
        v druhé ruce</label>
      <label>
        <input type="radio" value="<?= ItemHoldingCode::TWO_HANDS ?>"
               name="<?= FightController::RANGED_WEAPON_HOLDING ?>"
               <?php if ($controller->getFight()->getCurrentRangedWeaponHolding()->getValue() === ItemHoldingCode::TWO_HANDS) { ?>checked<?php } ?>>
        obouručně
      </label>
    </div>
    <div class="col">
      <label>
        <select name="<?= FightController::RANGED_FIGHT_SKILL ?>">
            <?php
            $selectedSkillForRanged = $controller->getFight()->getCurrentRangedSkillCode();
            foreach ($controller->getFight()->getSkillsForRanged() as $skillCode) { ?>
              <option value="<?= $skillCode->getValue() ?>"
                      <?php if ($selectedSkillForRanged->getValue() === $skillCode->getValue()) { ?>selected<?php } ?>>
                  <?= $skillCode->translateTo('cs') ?>
              </option>
            <?php } ?>
        </select>
      </label>
      <span data-history-skill-ranks="<?= htmlspecialchars($controller->getHistoryRangedSkillRanksJson()) ?>">
      <label>na stupni <input type="radio" value="0" name="<?= FightController::RANGED_FIGHT_SKILL_RANK ?>"
                              <?php if ($controller->getFight()->getCurrentRangedSkillRank() === 0) { ?>checked<?php } ?>>
        0,
      </label>
      <label><input type="radio" value="1" name="<?= FightController::RANGED_FIGHT_SKILL_RANK ?>"
                    <?php if ($controller->getFight()->getCurrentRangedSkillRank() === 1) { ?>checked<?php } ?>>
        1,
      </label>
      <label><input type="radio" value="2" name="<?= FightController::RANGED_FIGHT_SKILL_RANK ?>"
                    <?php if ($controller->getFight()->getCurrentRangedSkillRank() === 2) { ?>checked<?php } ?>>
        2,
      </label>
      <label><input type="radio" value="3" name="<?= FightController::RANGED_FIGHT_SKILL_RANK ?>"
                    <?php if ($controller->getFight()->getCurrentRangedSkillRank() === 3) { ?>checked<?php } ?>>
        3
      </label>
    </span>
    </div>
  </div>
  <div class="row">
    <div class="col-sm">
      <label>vzdálenost cíle <span class="hint">v metrech</span>
        <input type="number" name="<?= FightController::RANGED_TARGET_DISTANCE ?>" min="1" max="900" step="0.1"
               value="<?= $controller->getFight()->getCurrentTargetDistance()->getMeters() ?>">
      </label>
    </div>
    <div class="col-sm">
      <label>velikost cíle <span class="hint">(Vel)</span>
        <input type="number" name="<?= FightController::RANGED_TARGET_SIZE ?>"
               value="<?= $controller->getFight()->getCurrentTargetSize() ?>">
      </label>
    </div>
  </div>
  <div class="row">
      <?php
      /** @noinspection PhpUnusedLocalVariableInspection */
      $fightProperties = $controller->getFight()->getCurrentRangedFightProperties();
      /** @noinspection PhpUnusedLocalVariableInspection */
      $previousFightProperties = $controller->getFight()->getPreviousRangedFightProperties();
      include __DIR__ . '/fight_properties_trait.php'; ?>
    <div class="col-sm-3">
      Soubojový dostřel
      <img class="line-sized" src="/images/emojione/bow-and-arrow-1f3f9.png">
      <span class="<?= $controller->getClassForChangedValue($previousFightProperties->getEncounterRange(), $fightProperties->getEncounterRange()) ?>">
          <?= "{$fightProperties->getEncounterRange()} ({$fightProperties->getEncounterRange()->getInMeters(Tables::getIt())} m)" ?>
      </span>
    </div>
    <div class="col-sm-3">
      Maximální dostřel
      <img class="line-sized" src="/images/emojione/bow-and-arrow-1f3f9.png">
      <span class="<?= $controller->getClassForChangedValue($previousFightProperties->getMaximalRange(), $fightProperties->getMaximalRange()) ?>">
          <?= "{$fightProperties->getMaximalRange()} ({$fightProperties->getMaximalRange()->getInMeters(Tables::getIt())} m)" ?>
      </span>
    </div>
  </div>
</fieldset>