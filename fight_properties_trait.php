<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\DistanceUnitCode;
use DrdPlus\FightProperties\FightProperties;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

/**
 * @var Controller $controller
 * @var FightProperties $previousFightProperties
 * @var FightProperties $fightProperties
 */

$previousAttackNumber = $previousFightProperties->getAttackNumber(
    new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
    Size::getIt(0)
);
$currentAttackNumber = $fightProperties->getAttackNumber(
    new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
    Size::getIt(0)
);
?>
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