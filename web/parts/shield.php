<?php
namespace DrdPlus\FightCalculator;

/** @var FightController $controller */
?>

<div class="row">
  <h2 id="Štít" class="col"><a href="#Štít" class="inner">Štít</a></h2>
</div>
<fieldset>
    <?php
    if ($controller->isAddingNewShield()) {
        echo $controller->getAddCustomShieldContent();
    }
    foreach ($controller->getCurrentValues()->getCustomShieldsValues() as $shieldName => $shieldValues) {
        /** @var array|string[] $shieldValues */
        foreach ($shieldValues as $typeName => $shieldValue) { ?>
          <input type="hidden" name="<?= $typeName ?>[<?= $shieldName ?>]" value="<?= $shieldValue ?>">
        <?php }
    } ?>
  <div class="row <?php if ($controller->isAddingNewShield()) { ?>hidden<?php } ?>" id="chooseShield">
    <div class="col-sm">
      <a title="Přidat vlastní štít"
         href="<?= $controller->getLocalUrlWithQuery([FightController::ACTION => FightController::ADD_NEW_SHIELD]) ?>"
         class="button add">+</a>
      <label>
        <select name="<?= FightController::SHIELD ?>"><?php
            /** @var array $shield */
            foreach ($controller->getShields() as $shield) {
                $shieldCode = $shield['code']; ?>
              <option value="<?= $shieldCode->getValue() ?>"
                      <?php if ($controller->getFight()->getCurrentShield()->getValue() === $shieldCode->getValue()) { ?>selected<?php }
                      if (!$shield['canUseIt']) { ?>disabled<?php } ?>>
                  <?= (!$shield['canUseIt'] ? '💪 ' : '') . $shieldCode->translateTo('cs') . ($controller->getFight()->getCoverOfShield($shieldCode) > 0 ? (' +' . $controller->getFight()->getCoverOfShield($shieldCode)) : '') ?>
              </option>
            <?php } ?>
        </select>
      </label>
        <?php foreach ($controller->getMessagesAboutShields() as $messageAboutShield) { ?>
          <span class="info-message"><?= $messageAboutShield ?></span>
        <?php } ?>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <span class="keyword"><a href="https://pph.drdplus.info/#pouzivani_stitu" target="_blank">
          <?= $controller->getShieldUsageSkillCode()->translateTo('cs') ?></a>
      </span>
      <span data-history-skill-ranks="<?= \htmlspecialchars($controller->getHistoryShieldUsageSkillRanksJson()) ?>">
      <label>na stupni <input type="radio" value="0" name="<?= FightController::SHIELD_USAGE_SKILL_RANK ?>"
                              <?php if ($controller->getFight()->getCurrentShieldUsageSkillRank() === 0) { ?>checked<?php } ?>>
        0,
      </label>
      <label><input type="radio" value="1" name="<?= FightController::SHIELD_USAGE_SKILL_RANK ?>"
                    <?php if ($controller->getFight()->getCurrentShieldUsageSkillRank() === 1) { ?>checked<?php } ?>>
        1,
      </label>
      <label><input type="radio" value="2" name="<?= FightController::SHIELD_USAGE_SKILL_RANK ?>"
                    <?php if ($controller->getFight()->getCurrentShieldUsageSkillRank() === 2) { ?>checked<?php } ?>>
        2,
      </label>
      <label><input type="radio" value="3" name="<?= FightController::SHIELD_USAGE_SKILL_RANK ?>"
                    <?php if ($controller->getFight()->getCurrentShieldUsageSkillRank() === 3) { ?>checked<?php } ?>>
        3
      </label>
    </div>
    <div class="col">
      <label>
        <a class="keyword" target="_blank" href="https://pph.drdplus.info/#boj_se_zbrani">
            <?= $controller->getFightWithShieldsSkillCode()->translateTo('cs') ?>
        </a>
      </label>
      <span data-history-skill-ranks="<?= \htmlspecialchars($controller->getHistoryFightWithShieldSkillRanksJson()) ?>">
      <label>na stupni <input type="radio" value="0" name="<?= FightController::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                              <?php if ($controller->getFight()->getCurrentFightWithShieldsSkillRank() === 0) { ?>checked<?php } ?>>
        0,
      </label>
      <label><input type="radio" value="1" name="<?= FightController::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                    <?php if ($controller->getFight()->getCurrentFightWithShieldsSkillRank() === 1) { ?>checked<?php } ?>>
        1,
      </label>
      <label><input type="radio" value="2" name="<?= FightController::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                    <?php if ($controller->getFight()->getCurrentFightWithShieldsSkillRank() === 2) { ?>checked<?php } ?>>
        2,
      </label>
      <label><input type="radio" value="3" name="<?= FightController::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                    <?php if ($controller->getFight()->getCurrentFightWithShieldsSkillRank() === 3) { ?>checked<?php } ?>>
        3
      </label>
    </div>
  </div>
  <div class="row <?php if ($controller->getFight()->getCurrentShield()->isUnarmed()) { ?>hidden<?php } ?>">
    <div class="col">
        <?php
        /** @noinspection PhpUnusedLocalVariableInspection */
        $currentShieldFightProperties = $controller->getFight()->getCurrentMeleeShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousShieldFightProperties = $controller->getFight()->getPreviousMeleeShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $currentShieldHolding = $controller->getFight()->getCurrentMeleeShieldHolding();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousShieldHolding = $controller->getFight()->getPreviousArmaments()->getPreviousMeleeShieldHolding();
        ?>
      <div class="row">
        <h4 class="col">štít se zbraní na blízko</h4>
      </div>
      <div class="row">
          <?php include __DIR__ . '/shield_fight_properties_trait.php' ?>
      </div>
    </div>
    <div class="col">
        <?php
        /** @noinspection PhpUnusedLocalVariableInspection */
        $currentShieldFightProperties = $controller->getFight()->getRangedShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousShieldFightProperties = $controller->getFight()->getPreviousRangedShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $currentShieldHolding = $controller->getFight()->getCurrentRangedShieldHolding();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousShieldHolding = $controller->getFight()->getPreviousArmaments()->getPreviousRangedShieldHolding();
        ?>
      <div class="row">
        <div class="col"><h4>štít se zbraní na dálku</h4></div>
      </div>
      <div class="row">
          <?php include __DIR__ . '/shield_fight_properties_trait.php' ?>
      </div>
    </div>
</fieldset>