<?php
namespace DrdPlus\FightCalculator;

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\ItemHoldingCode;

/** @var FightController $controller */
$selectedMeleeWeapon = $controller->getFight()->getCurrentMeleeWeapon();
$selectedMeleeWeaponValue = $selectedMeleeWeapon ? $selectedMeleeWeapon->getValue() : null;
?>
<div class="row">
  <h2 id="Na blízko" class="col"><a href="#Na blízko" class="inner">Na blízko</a></h2>
</div>
<fieldset>
    <?php if ($controller->isAddingNewMeleeWeapon()) {
        echo $controller->getAddCustomMeleeWeaponContent();
    }
    foreach ($controller->getCurrentValues()->getCustomMeleeWeaponsValues() as $weaponName => $weaponValues) {
        /** @var array|string[] $weaponValues */
        foreach ($weaponValues as $typeName => $weaponValue) { ?>
          <input type="hidden" name="<?= $typeName ?>[<?= $weaponName ?>]" value="<?= $weaponValue ?>">
        <?php }
    } ?>
  <div class="<?php if ($controller->isAddingNewMeleeWeapon()) { ?>hidden<?php } ?>" id="chooseMeleeWeapon">
    <div class="row">
      <div class="col">
        <a title="Přidat vlastní zbraň na blízko"
           href="<?= $controller->getLocalUrlWithQuery([FightController::ACTION => FightController::ADD_NEW_MELEE_WEAPON]) ?>"
           class="button add">+</a>
        <label>
          <select name="<?= FightController::MELEE_WEAPON ?>" title="Melee weapon">
              <?php /** @var array $meleeWeaponsFromCategory */
              foreach ($controller->getMeleeWeapons() as $weaponCategory => $meleeWeaponsFromCategory) { ?>
                <optgroup label="<?= WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2) ?>">
                    <?php /** @var array $meleeWeapon */
                    foreach ($meleeWeaponsFromCategory as $meleeWeapon) {
                        /** @var MeleeWeaponCode $meleeWeaponCode */
                        $meleeWeaponCode = $meleeWeapon['code']; ?>
                      <option value="<?= $meleeWeaponCode->getValue() ?>"
                              <?php if ($selectedMeleeWeaponValue === $meleeWeaponCode->getValue()) { ?>selected<?php }
                              if (!$meleeWeapon['canUseIt']) { ?>disabled<?php } ?>>
                          <?= (!$meleeWeapon['canUseIt'] ? '💪 ' : '') . $meleeWeaponCode->translateTo('cs') ?>
                      </option>
                    <?php } ?>
                </optgroup>
              <?php } ?>
          </select>
        </label>
          <?php foreach ($controller->getMessagesAboutMeleeWeapons() as $messagesAboutMeleeWeapon) { ?>
            <div class="info-message"><?= $messagesAboutMeleeWeapon ?></div>
          <?php } ?>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <label>
          <input type="radio" value="<?= ItemHoldingCode::MAIN_HAND ?>"
                 name="<?= FightController::MELEE_WEAPON_HOLDING ?>"
                 <?php if ($controller->getFight()->getCurrentMeleeWeaponHolding()->getValue() === ItemHoldingCode::MAIN_HAND) { ?>checked<?php } ?>>
          v dominantní ruce</label>
        <label>
          <input type="radio" value="<?= ItemHoldingCode::OFFHAND ?>" name="<?= FightController::MELEE_WEAPON_HOLDING ?>"
                 <?php if ($controller->getFight()->getCurrentMeleeWeaponHolding()->getValue() === ItemHoldingCode::OFFHAND) { ?>checked<?php } ?>>
          v druhé ruce</label>
        <label>
          <input type="radio" value="<?= ItemHoldingCode::TWO_HANDS ?>"
                 name="<?= FightController::MELEE_WEAPON_HOLDING ?>"
                 <?php if ($controller->getFight()->getCurrentMeleeWeaponHolding()->getValue() === ItemHoldingCode::TWO_HANDS) { ?>checked<?php } ?>>
          obouručně
        </label>
      </div>
      <div class="col">
        <label>
          <select name="<?= FightController::MELEE_FIGHT_SKILL ?>">
              <?php
              $selectedSkillForMelee = $controller->getFight()->getCurrentMeleeSkillCode();
              foreach ($controller->getFight()->getPossibleSkillsForMelee() as $skillCode) { ?>
                <option value="<?= $skillCode->getValue() ?>"
                        <?php if ($selectedSkillForMelee->getValue() === $skillCode->getValue()) { ?>selected<?php } ?>>
                    <?= $skillCode->translateTo('cs') ?>
                </option>
              <?php } ?>
          </select>
        </label>
        <span class="skill-ranks" data-history-skill-ranks="<?= htmlspecialchars($controller->getHistoryMeleeSkillRanksJson()) ?>">
          <label>na stupni <input type="radio" value="0" name="<?= FightController::MELEE_FIGHT_SKILL_RANK ?>"
                                  <?php if ($controller->getFight()->getCurrentMeleeSkillRank() === 0) { ?>checked<?php } ?>>0,
          </label>
          <label><input type="radio" value="1" name="<?= FightController::MELEE_FIGHT_SKILL_RANK ?>"
                        <?php if ($controller->getFight()->getCurrentMeleeSkillRank() === 1) { ?>checked<?php } ?>>1,
          </label>
          <label><input type="radio" value="2" name="<?= FightController::MELEE_FIGHT_SKILL_RANK ?>"
                        <?php if ($controller->getFight()->getCurrentMeleeSkillRank() === 2) { ?>checked<?php } ?>>2,
          </label>
          <label><input type="radio" value="3" name="<?= FightController::MELEE_FIGHT_SKILL_RANK ?>"
                        <?php if ($controller->getFight()->getCurrentMeleeSkillRank() === 3) { ?>checked<?php } ?>>3
          </label>
        </span>
      </div>
    </div>
  </div>
  <div class="with-skill-ranks row">
      <?php
      /** @noinspection PhpUnusedLocalVariableInspection */
      $fightProperties = $controller->getFight()->getMeleeWeaponFightProperties();
      /** @noinspection PhpUnusedLocalVariableInspection */
      $previousFightProperties = $controller->getFight()->getPreviousMeleeWeaponFightProperties();
      include __DIR__ . '/fight_properties_trait.php'; ?>
  </div>
</fieldset>