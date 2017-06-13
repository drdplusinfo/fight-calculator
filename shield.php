<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\DistanceUnitCode;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

/** @var Controller $controller */
?>

<div class="panel">
    <label>
        <select
                name="<?= $controller::SHIELD ?>"><?php foreach ($controller->getPossibleShields() as $shield) { ?>
                <option value="<?= $shield->getValue() ?>"
                        <?php if ($controller->getSelectedShield()->getValue() === $shield->getValue()){ ?>selected<?php } ?>>
                    <?= $shield->translateTo('cs') ?>
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
        $meleeShieldFightProperties = $controller->getMeleeShieldFightProperties();
        $previousMeleeShieldFightProperties = $controller->getPreviousMeleeShieldFightProperties();
        $currentAttackNumber = $meleeShieldFightProperties->getAttackNumber(
            new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
            Size::getIt(0)
        );
        $previousAttackNumber = $previousMeleeShieldFightProperties->getAttackNumber(
            new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
            Size::getIt(0)
        );
        ?>
        <thead>
        <tr>
            <th colspan="100%"><h4>štít se zbraní na blízko</h4></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>BČ</td>
            <td><img class="line-sized" src="images/emojione/fight-2694.png"></td>
            <td <?php if ($previousMeleeShieldFightProperties->getFightNumber()->getValue() !== $meleeShieldFightProperties->getFightNumber()->getValue()) { ?>class="changed" <?php } ?>>
                <?= $meleeShieldFightProperties->getFightNumber() ?>
            </td>
            <td><span class="hint">se štítem jako zbraň</span></td>
        </tr>
        <tr>
            <td>ÚČ</td>
            <td><img class="line-sized" src="images/emojione/fight-number-1f624.png"></td>
            <td <?php if ($previousAttackNumber->getValue() !== $currentAttackNumber->getValue()) { ?>class="changed" <?php } ?>>
                <?= $currentAttackNumber ?>
            </td>
            <td><span class="hint">se štítem jako zbraň</span></td>
        </tr>
        <tr>
            <td>ZZ</td>
            <td><img class="line-sized" src="images/emojione/base-of-wounds-1f480.png"></td>
            <td <?php if ($previousMeleeShieldFightProperties->getBaseOfWounds()->getValue() !== $meleeShieldFightProperties->getBaseOfWounds()->getValue()) { ?>class="changed" <?php } ?>>
                <?= $meleeShieldFightProperties->getBaseOfWounds() ?>
            </td>
            <td><span class="hint">se štítem jako zbraň</span></td>
        </tr>
        <tr>
            <td>OČ</td>
            <td><img class="line-sized" src="images/emojione/defense-number-1f6e1.png"></td>
            <td <?php if ($previousMeleeShieldFightProperties->getDefenseNumberWithShield()->getValue() !== $meleeShieldFightProperties->getDefenseNumberWithShield()->getValue()) { ?>class="changed" <?php } ?>>
                <?= $meleeShieldFightProperties->getDefenseNumberWithShield() ?>
            </td>
            <td></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="100%">
                držen <span
                        class="keyword <?php if ($controller->getPreviousMeleeShieldHolding()->getValue() !== $controller->getCurrentMeleeShieldHolding()->getValue()) { ?> changed<?php } ?>">
                    <?= $controller->getCurrentMeleeShieldHolding()->translateTo('cs') ?>
                </span>
            </td>
        </tr>
        </tfoot>
    </table>
    <table class="panel result">
        <?php
        $rangedShieldFightProperties = $controller->getRangedShieldFightProperties();
        $previousRangedShieldFightProperties = $controller->getPreviousRangedShieldFightProperties();
        $currentAttackNumber = $rangedShieldFightProperties->getAttackNumber(
            new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
            Size::getIt(0)
        );
        $previousAttackNumber = $previousRangedShieldFightProperties->getAttackNumber(
            new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
            Size::getIt(0)
        )
        ?>
        <thead>
        <tr>
            <th colspan="100%"><h4>štít se zbraní na dálku</h4></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>BČ</td>
            <td><img class="line-sized" src="images/emojione/fight-2694.png"></td>
            <td <?php if ($previousRangedShieldFightProperties->getFightNumber()->getValue() !== $rangedShieldFightProperties->getFightNumber()->getValue()) { ?>class="changed" <?php } ?>>
                <?= $rangedShieldFightProperties->getFightNumber() ?>
            </td>
            <td><span class="hint">se štítem jako zbraň</span></td>
        </tr>
        <tr>
            <td>ÚČ</td>
            <td><img class="line-sized" src="images/emojione/fight-number-1f624.png"></td>
            <td <?php if ($previousAttackNumber->getValue() !== $currentAttackNumber->getValue()) { ?>class="changed" <?php } ?>>
                <?= $currentAttackNumber ?>
            </td>
            <td><span class="hint">se štítem jako zbraň</span></td>
        </tr>
        <tr>
            <td>ZZ</td>
            <td><img class="line-sized" src="images/emojione/base-of-wounds-1f480.png"></td>
            <td <?php if ($previousRangedShieldFightProperties->getBaseOfWounds()->getValue() !== $rangedShieldFightProperties->getBaseOfWounds()->getValue()) { ?>class="changed" <?php } ?>>
                <?= $rangedShieldFightProperties->getBaseOfWounds() ?>
            </td>
            <td><span class="hint">se štítem jako zbraň</span></td>
        </tr>
        <tr>
            <td>OČ</td>
            <td><img class="line-sized" src="images/emojione/defense-number-1f6e1.png"></td>
            <td <?php if ($previousRangedShieldFightProperties->getDefenseNumberWithShield()->getValue() !== $rangedShieldFightProperties->getDefenseNumberWithShield()->getValue()) { ?>class="changed" <?php } ?>>
                <?= $rangedShieldFightProperties->getDefenseNumberWithShield() ?>
            </td>
            <td></td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="100%">
                držen <span
                        class="keyword <?php if ($controller->getPreviousRangedShieldHolding()->getValue() !== $controller->getCurrentRangedShieldHolding()->getValue()) { ?> changed<?php } ?>">
                    <?= $controller->getCurrentRangedShieldHolding()->translateTo('cs') ?>
                </span>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="block"><input type="submit" value="Přepočítat"></div>
