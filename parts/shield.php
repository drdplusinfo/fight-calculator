<?php
namespace DrdPlus\Fight;

/** @var Controller $controller */
?>

<div class="panel">
    <label class="block">
        <select name="<?= Controller::SHIELD ?>"><?php
            /** @var array $shield */
            foreach ($controller->getShields() as $shield) {
                $shieldCode = $shield['code']; ?>
                <option value="<?= $shieldCode->getValue() ?>"
                        <?php if ($controller->getFight()->getSelectedShield()->getValue() === $shieldCode->getValue()) { ?>selected<?php }
                        if (!$shield['canUseIt']) { ?>disabled<?php } ?>>
                    <?= $shieldCode->translateTo('cs') . ($controller->getFight()->getCoverOfShield($shieldCode) > 0 ? (' +' . $controller->getFight()->getCoverOfShield($shieldCode)) : '') ?>
                </option>
            <?php } ?>
        </select>
    </label>
</div>
<div class="panel">
    <div class="panel">
        dovednost <span class="keyword"><?= $controller->getShieldUsageSkillCode()->translateTo('cs') ?></span>
    </div>
    <div class="panel">
        <label>na stupni <input type="radio" value="0" name="<?= Controller::SHIELD_USAGE_SKILL_RANK ?>"
                                <?php if ($controller->getFight()->getSelectedShieldUsageSkillRank() === 0) { ?>checked<?php } ?>>
            0,
        </label>
        <label><input type="radio" value="1" name="<?= Controller::SHIELD_USAGE_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedShieldUsageSkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= Controller::SHIELD_USAGE_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedShieldUsageSkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= Controller::SHIELD_USAGE_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedShieldUsageSkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
</div>
<div class="block info-messages">
    <?php foreach ($controller->getMessagesAboutShields() as $messageAboutShield) { ?>
        <div class="info-message"><?= $messageAboutShield ?></div>
    <?php } ?>
</div>
<div class="block skill">
    <div class="panel">
        <label>
            <span class="keyword"><?= $controller->getFightWithShieldsSkillCode()->translateTo('cs') ?></span>
        </label>
    </div>
    <div class="panel">
        <label>na stupni <input type="radio" value="0" name="<?= Controller::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                                <?php if ($controller->getFight()->getSelectedFightWithShieldsSkillRank() === 0) { ?>checked<?php } ?>>
            0,
        </label>
        <label><input type="radio" value="1" name="<?= Controller::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedFightWithShieldsSkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= Controller::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedFightWithShieldsSkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= Controller::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedFightWithShieldsSkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
</div>
<div class="block <?php if ($controller->getFight()->getSelectedShield()->isUnarmed()) { ?>hidden<?php } ?>">
    <table class="panel result">
        <?php
        /** @noinspection PhpUnusedLocalVariableInspection */
        $fightProperties = $controller->getFight()->getMeleeShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousFightProperties = $controller->getFight()->getPreviousMeleeShieldFightProperties();
        ?>
        <thead>
        <tr>
            <th colspan="100%"><h4>štít se zbraní na blízko</h4></th>
        </tr>
        </thead>
        <tbody>
        <?php include __DIR__ . '/shield_fight_properties_trait.php' ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="100%">
                držen <span
                        class="keyword <?php if ($controller->getFight()->getPreviousMeleeShieldHolding()->getValue() !== $controller->getFight()->getSelectedMeleeShieldHolding()->getValue()) { ?> changed <?php } ?>">
                    <?= $controller->getFight()->getSelectedMeleeShieldHolding()->translateTo('cs') ?>
                </span>
            </td>
        </tr>
        </tfoot>
    </table>
    <table class="panel result">
        <?php
        /** @noinspection PhpUnusedLocalVariableInspection */
        $fightProperties = $controller->getFight()->getRangedShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousFightProperties = $controller->getFight()->getPreviousRangedShieldFightProperties();
        ?>
        <thead>
        <tr>
            <th colspan="100%"><h4>štít se zbraní na dálku</h4></th>
        </tr>
        </thead>
        <tbody>
        <?php include __DIR__ . '/shield_fight_properties_trait.php' ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="100%">
                držen <span
                        class="keyword <?php if ($controller->getFight()->getPreviousRangedShieldHolding()->getValue() !== $controller->getFight()->getSelectedRangedShieldHolding()->getValue()) { ?> changed <?php } ?>">
                    <?= $controller->getFight()->getSelectedRangedShieldHolding()->translateTo('cs') ?>
                </span>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="block"><input type="submit" value="Přepočítat"></div>
