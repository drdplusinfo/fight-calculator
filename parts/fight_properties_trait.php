<?php
namespace DrdPlus\Fight;

use DrdPlus\FightProperties\FightProperties;

/**
 * @var Controller $controller
 * @var FightProperties $previousFightProperties
 * @var FightProperties $fightProperties
 */

$previousAttackNumber = $previousFightProperties->getAttackNumber(
    $controller->getFight()->getPreviousTargetDistance(), // melee attack is not affected by this
    $controller->getFight()->getPreviousTargetSize() // melee attack is not affected by this
);
$currentAttackNumber = $fightProperties->getAttackNumber(
    $controller->getFight()->getCurrentTargetDistance(), // melee attack is not affected by this
    $controller->getFight()->getCurrentTargetSize() // melee attack is not affected by this
);
?>
<tr>
    <td>BČ</td>
    <td><img class="line-sized" src="images/emojione/fight-2694.png"></td>
    <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getFightNumber(), $fightProperties->getFightNumber()) ?>">
        <?= $fightProperties->getFightNumber() ?>
    </td>
</tr>
<tr>
    <td>ÚČ</td>
    <td><img class="line-sized" src="images/emojione/fight-number-1f624.png"></td>
    <td class="<?= $controller->getClassForChangedValue($previousAttackNumber, $currentAttackNumber) ?>">
        <?= $currentAttackNumber ?>
    </td>
</tr>
<tr>
    <td>ZZ</td>
    <td><img class="line-sized" src="images/emojione/base-of-wounds-1f480.png"></td>
    <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getBaseOfWounds(), $fightProperties->getBaseOfWounds()) ?>">
        <?= $fightProperties->getBaseOfWounds() ?>
    </td>
</tr>
<tr>
    <td>OČ</td>
    <td><img class="line-sized" src="images/emojione/defense-number-1f6e1.png"></td>
    <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getDefenseNumberWithWeaponlike(), $fightProperties->getDefenseNumberWithWeaponlike()) ?>">
        <?= $fightProperties->getDefenseNumberWithWeaponlike() ?>
    </td>
</tr>