<?php
namespace DrdPlus\Fight;
/** @var Controller $controller */
?>
<div class="panel">
    <label><select name="<?= $controller::BODY_ARMOR ?>">
            <?php foreach ($controller->getPossibleBodyArmors() as $bodyArmor) { ?>
                <option value="<?= $bodyArmor->getValue() ?>"
                        <?php if ($controller->getSelectedBodyArmor()->getValue() === $bodyArmor->getValue()){ ?>selected<?php } ?>>
                    <?= $bodyArmor->translateTo('cs') . ' ' . ($controller->getProtectionOfBodyArmor($bodyArmor) > 0 ? ('+' . $controller->getProtectionOfBodyArmor($bodyArmor)) : '') ?>
                </option>
            <?php } ?>
        </select>
    </label>
</div>
<div class="panel">
    <label>
        <select name="<?= $controller::HELM ?>">
            <?php foreach ($controller->getPossibleHelms() as $helm) { ?>
                <option value="<?= $helm->getValue() ?>"
                        <?php if ($controller->getSelectedHelm()->getValue() === $helm->getValue()){ ?>selected<?php } ?>>
                    <?= $helm->translateTo('cs') . ' ' . ($controller->getProtectionOfHelm($helm) > 0 ? ('+' . $controller->getProtectionOfHelm($helm)) : '') ?>
                </option>
            <?php } ?>
        </select>
    </label>
</div>
<div class="block skill">
    <div class="panel">
        <label>
            <span class="keyword"><?= $controller->getPossibleSkillForArmor()->translateTo('cs') ?></span>
        </label>
    </div>
    <div class="panel">
        <label>na stupni <input type="radio" value="0" name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                                <?php if ($controller->getSelectedArmorSkillRank() === 0) { ?>checked<?php } ?>> 0,
        </label>
        <label><input type="radio" value="1" name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                      <?php if ($controller->getSelectedArmorSkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                      <?php if ($controller->getSelectedArmorSkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                      <?php if ($controller->getSelectedArmorSkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
</div>
<div class="block"><input type="submit" value="Přepočítat"></div>
