<?php
namespace DrdPlus\Calculator\Fight;
/** @var Controller $controller */
if ($controller->isAddingNewBodyArmor()) { ?>
  <div id="addBodyArmor" class="block add">
      <?php include __DIR__ . '/add_custom_body_armor.php' ?>
  </div>
<?php }
foreach ($controller->getCurrentValues()->getCustomBodyArmorsValues() as $armorName => $armorValues) {
    /** @var array|string[] $armorValues */
    foreach ($armorValues as $typeName => $armorValue) { ?>
      <input type="hidden" name="<?= $typeName ?>[<?= $armorName ?>]" value="<?= $armorValue ?>">
    <?php }
} ?>
<div class="block <?php if ($controller->isAddingNewBodyArmor() || $controller->isAddingNewHelm()) { ?>hidden<?php } ?>"
     id="chooseBodyArmor">
  <div class="panel">
    <a title="Přidat vlastní zbroj"
       href="<?= $controller->getCurrentUrlWithQuery([Controller::ACTION => Controller::ADD_NEW_BODY_ARMOR]) ?>"
       class="button add">+</a>
    <label>
      <select name="<?= Controller::BODY_ARMOR ?>">
          <?php /** @var array $bodyArmor */
          foreach ($controller->getBodyArmors() as $bodyArmor) {
              $bodyArmorCode = $bodyArmor['code']; ?>
            <option value="<?= $bodyArmorCode->getValue() ?>"
                    <?php if ($controller->getFight()->getCurrentBodyArmor()->getValue() === $bodyArmorCode->getValue()) { ?>selected<?php }
                    if (!$bodyArmor['canUseIt']) { ?>disabled<?php } ?>>
                <?= (!$bodyArmor['canUseIt'] ? '💪 ' : '') . $bodyArmorCode->translateTo('cs') . ($controller->getFight()->getProtectionOfBodyArmor($bodyArmorCode) > 0 ? (' +' . $controller->getFight()->getProtectionOfBodyArmor($bodyArmorCode)) : '') ?>
            </option>
          <?php } ?>
      </select>
    </label>
  </div>
  <div class="block info-messages">
      <?php foreach ($controller->getMessagesAboutArmors() as $messageAboutArmor) { ?>
        <div class="info-message"><?= $messageAboutArmor ?></div>
      <?php } ?>
  </div>
</div>
<?php
if ($controller->isAddingNewHelm()) { ?>
  <div id="addHelm" class="block add">
      <?php include __DIR__ . '/add_custom_helm.php' ?>
  </div>
<?php }
foreach ($controller->getCurrentValues()->getCustomHelmsValues() as $helmName => $helmValues) {
    /** @var array|string[] $helmValues */
    foreach ($helmValues as $typeName => $helmValue) { ?>
      <input type="hidden" name="<?= $typeName ?>[<?= $helmName ?>]" value="<?= $helmValue ?>">
    <?php }
} ?>
<div class="block <?php if ($controller->isAddingNewBodyArmor() || $controller->isAddingNewHelm()) { ?>hidden<?php } ?>"
     id="chooseHelm">
  <div class="panel">
    <a title="Přidat vlastní helmu"
       href="<?= $controller->getCurrentUrlWithQuery([Controller::ACTION => Controller::ADD_NEW_HELM]) ?>"
       class="button add">+</a>
    <label>
      <select name="<?= Controller::HELM ?>">
          <?php /** @var array $helm */
          foreach ($controller->getHelms() as $helm) {
              $helmCode = $helm['code']; ?>
            <option value="<?= $helmCode->getValue() ?>"
                    <?php if ($controller->getFight()->getCurrentHelm()->getValue() === $helmCode->getValue()) { ?>selected<?php }
                    if (!$helm['canUseIt']) { ?>disabled<?php } ?>>
                <?= (!$helm['canUseIt'] ? '💪 ' : '') . $helmCode->translateTo('cs') . ($controller->getFight()->getProtectionOfHelm($helmCode) > 0 ? (' +' . $controller->getFight()->getProtectionOfHelm($helmCode)) : '') ?>
            </option>
          <?php } ?>
      </select>
    </label>
  </div>
  <div class="block info-messages">
      <?php foreach ($controller->getMessagesAboutHelms() as $messageAboutHelm) { ?>
        <div class="info-message"><?= $messageAboutHelm ?></div>
      <?php } ?>
  </div>
</div>
<div class="block skill">
  <div class="panel">
    <label>
            <span class="keyword"><a target="_blank" href="https://pph.drdplus.info/#noseni_zbroje">
                    <?= $controller->getFight()->getSkillForArmor()->translateTo('cs') ?></a>
            </span>
    </label>
  </div>
  <div class="panel">
    <label>na stupni <input type="radio" value="0" name="<?= Controller::ARMOR_SKILL_VALUE ?>"
                            <?php if ($controller->getFight()->getCurrentArmorSkillRank() === 0) { ?>checked<?php } ?>>
      0,
    </label>
    <label><input type="radio" value="1" name="<?= Controller::ARMOR_SKILL_VALUE ?>"
                  <?php if ($controller->getFight()->getCurrentArmorSkillRank() === 1) { ?>checked<?php } ?>> 1,
    </label>
    <label><input type="radio" value="2" name="<?= Controller::ARMOR_SKILL_VALUE ?>"
                  <?php if ($controller->getFight()->getCurrentArmorSkillRank() === 2) { ?>checked<?php } ?>> 2,
    </label>
    <label><input type="radio" value="3" name="<?= Controller::ARMOR_SKILL_VALUE ?>"
                  <?php if ($controller->getFight()->getCurrentArmorSkillRank() === 3) { ?>checked<?php } ?>> 3
    </label>
  </div>
</div>
<div class="block"><input type="submit" value="Přepočítat"></div>