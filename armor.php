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
        dovednost <span class="keyword"><?= $controller->getPossibleSkillForArmor()->translateTo('cs') ?></span> na
        stupni <input
                type="number" min="0" max="3"
                name="<?= $controller::ARMOR_SKILL_VALUE ?>"
                value="<?= $controller->getSelectedArmorSkillRank() ?>">
    </label>
</div>
<div><input type="submit" value="OK"></div>
