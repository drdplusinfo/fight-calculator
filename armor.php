<?php
namespace DrdPlus\Fight;
/** @var Controller $controller */
?>
<div class="panel">
    <label><select
                name="<?= $controller::BODY_ARMOR ?>"><?php foreach ($controller->getPossibleBodyArmors() as $bodyArmor) {
                ?>
            <option value="<?= $bodyArmor->getValue() ?>"
                    <?php if ($controller->getSelectedBodyArmor()->getValue() === $bodyArmor->getValue()){ ?>selected<?php } ?>>
                <?= $bodyArmor->translateTo('cs') ?></option><?php
            } ?>
        </select>
    </label>
</div>
<div class="panel">
    <label><select
                name="<?= $controller::HELM ?>"><?php foreach ($controller->getPossibleHelms() as $helm) { ?>
                <option value="<?= $helm->getValue() ?>"
                        <?php if ($controller->getSelectedHelm()->getValue() === $helm->getValue()){ ?>selected<?php } ?>>
                    <?= $helm->translateTo('cs') ?></option>
            <?php } ?>
        </select>
    </label>
</div>
<div class="panel">
    <label>
        dovednost <span class="keyword"><?= $controller->getPossibleSkillForArmor()->translateTo('cs') ?></span>
    </label>
    <label>na stupni 0 <input type="radio" value="0" name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                              <?php if ($controller->getSelectedArmorSkillRank() === 0) { ?>checked<?php } ?>>
    </label>
    <label>1 <input type="radio" value="1" name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                    <?php if ($controller->getSelectedArmorSkillRank() === 1) { ?>checked<?php } ?>>
    </label>
    <label>2 <input type="radio" value="2" name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                    <?php if ($controller->getSelectedArmorSkillRank() === 2) { ?>checked<?php } ?>>
    </label>
    <label>3 <input type="radio" value="3" name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                    <?php if ($controller->getSelectedArmorSkillRank() === 3) { ?>checked<?php } ?>>
    </label>
</div>
<div class="block"><input type="submit" value="OK"></div>
