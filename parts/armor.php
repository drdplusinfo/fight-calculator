<?php
namespace DrdPlus\Fight;
/** @var Controller $controller */
?>
<div class="panel">
    <label class="block">
        <select name="<?= Controller::BODY_ARMOR ?>">
            <?php /** @var array $bodyArmor */
            foreach ($controller->getBodyArmors() as $bodyArmor) {
                $bodyArmorCode = $bodyArmor['code']; ?>
                <option value="<?= $bodyArmorCode->getValue() ?>"
                        <?php if ($controller->getFight()->getSelectedBodyArmor()->getValue() === $bodyArmorCode->getValue()) { ?>selected<?php }
                        if (!$bodyArmor['canUseIt']) { ?>disabled<?php } ?>>
                    <?= $bodyArmorCode->translateTo('cs') . ($controller->getFight()->getProtectionOfBodyArmor($bodyArmorCode) > 0 ? (' +' . $controller->getFight()->getProtectionOfBodyArmor($bodyArmorCode)) : '') ?>
                </option>
            <?php } ?>
        </select>
    </label>
    <div class="block info-messages">
        <?php foreach ($controller->getMessagesAboutArmors() as $messageAboutArmor) { ?>
            <div class="info-message"><?= $messageAboutArmor ?></div>
        <?php } ?>
    </div>
</div>
<div class="panel">
    <label class="block">
        <select name="<?= Controller::HELM ?>">
            <?php /** @var array $helm */
            foreach ($controller->getHelms() as $helm) {
                $helmCode = $helm['code']; ?>
                <option value="<?= $helmCode->getValue() ?>"
                        <?php if ($controller->getFight()->getSelectedHelm()->getValue() === $helmCode->getValue()) { ?>selected<?php }
                        if (!$helm['canUseIt']) { ?>disabled<?php } ?>>
                    <?= $helmCode->translateTo('cs') . ($controller->getFight()->getProtectionOfHelm($helmCode) > 0 ? (' +' . $controller->getFight()->getProtectionOfHelm($helmCode)) : '') ?>
                </option>
            <?php } ?>
        </select>
    </label>
    <div class="block info-messages">
        <?php foreach ($controller->getMessagesAboutHelms() as $messageAboutHelm) { ?>
            <div class="info-message"><?= $messageAboutHelm ?></div>
        <?php } ?>
    </div>
</div>
<div class="block skill">
    <div class="panel">
        <label>
            <span class="keyword"><?= $controller->getFight()->getSkillForArmor()->translateTo('cs') ?></span>
        </label>
    </div>
    <div class="panel">
        <label>na stupni <input type="radio" value="0" name="<?= Controller::ARMOR_SKILL_VALUE ?>"
                                <?php if ($controller->getFight()->getCurrentArmorSkillRank() === 0) { ?>checked<?php } ?>> 0,
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