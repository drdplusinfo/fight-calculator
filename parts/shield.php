<?php
namespace DrdPlus\Fight;

/** @var Controller $controller */
?>

<div class="panel">
    <label>
        <select name="<?= $controller::SHIELD ?>"><?php
            /** @var array $shield */
            foreach ($controller->getShields() as $shield) {

                $shieldCode = $shield['code']; ?>
                <option value="<?= $shieldCode->getValue() ?>"
                        <?php if ($controller->getSelectedShield()->getValue() === $shieldCode->getValue()) { ?>selected<?php }
                        if (!$shield['canUseIt']) { ?>disabled<?php } ?>>
                    <?= $shieldCode->translateTo('cs') . ($controller->getCoverOfShield($shieldCode) > 0 ? (' +' . $controller->getCoverOfShield($shieldCode)) : '') ?>
                </option>
            <?php } ?>
        </select>
    </label>
</div>
<?php if ($controller->getSelectedShield()->isUnarmed()) {
    return; // we will not show details about shield if no has been picked
} ?>
<div class="panel">
    <div class="panel">
        dovednost <span class="keyword"><?= $controller->getShieldUsageSkillCode()->translateTo('cs') ?></span>
    </div>
    <div class="panel">
        <label>na stupni <input type="radio" value="0" name="<?= $controller::SHIELD_USAGE_SKILL_RANK ?>"
                                <?php if ($controller->getSelectedShieldUsageSkillRank() === 0) { ?>checked<?php } ?>>
            0,
        </label>
        <label><input type="radio" value="1" name="<?= $controller::SHIELD_USAGE_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedShieldUsageSkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= $controller::SHIELD_USAGE_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedShieldUsageSkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= $controller::SHIELD_USAGE_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedShieldUsageSkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
</div>
<div class="block skill">
    <div class="panel">
        <label>
            <span class="keyword"><?= $controller->getFightWithShieldsSkillCode()->translateTo('cs') ?></span>
        </label>
    </div>
    <div class="panel">
        <label>na stupni <input type="radio" value="0" name="<?= $controller::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                                <?php if ($controller->getSelectedFightWithShieldsSkillRank() === 0) { ?>checked<?php } ?>>
            0,
        </label>
        <label><input type="radio" value="1" name="<?= $controller::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedFightWithShieldsSkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= $controller::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedFightWithShieldsSkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= $controller::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                      <?php if ($controller->getSelectedFightWithShieldsSkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
</div>

<div class="block">
    <table class="panel result">
        <?php
        /** @noinspection PhpUnusedLocalVariableInspection */
        $fightProperties = $controller->getMeleeShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousFightProperties = $controller->getPreviousMeleeShieldFightProperties();
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
                        class="keyword <?php if ($controller->getPreviousMeleeShieldHolding()->getValue() !== $controller->getSelectedMeleeShieldHolding()->getValue()) { ?> changed <?php } ?>">
                    <?= $controller->getSelectedMeleeShieldHolding()->translateTo('cs') ?>
                </span>
            </td>
        </tr>
        </tfoot>
    </table>
    <table class="panel result">
        <?php
        /** @noinspection PhpUnusedLocalVariableInspection */
        $fightProperties = $controller->getRangedShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousFightProperties = $controller->getPreviousRangedShieldFightProperties();
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
                        class="keyword <?php if ($controller->getPreviousRangedShieldHolding()->getValue() !== $controller->getSelectedRangedShieldHolding()->getValue()) { ?> changed <?php } ?>">
                    <?= $controller->getSelectedRangedShieldHolding()->translateTo('cs') ?>
                </span>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="block"><input type="submit" value="Přepočítat"></div>
