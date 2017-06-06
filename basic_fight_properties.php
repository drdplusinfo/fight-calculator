<?php
namespace DrdPlus\Fight;

use DrdPlus\Tables\Tables;

/** @var Controller $controller */
?>
<h2 id="Obecně"><a class="inner" href="#Obecně">Obecně</a></h2>
<table class="block result shortened">
    <?php $fightProperties = $controller->getGenericFightProperties() ?>
    <tbody>
    <tr>
        <td>Boj</td>
        <td><?= $fightProperties->getFight() ?></td>
        <td><span class="hint">(není ovlivněn výzbrojí)</span></td>
    </tr>
    <tr>
        <td>Útok</td>
        <td><?= $fightProperties->getAttack() ?></td>
        <td><span class="hint">(není ovlivněn výzbrojí)</span></td>
    </tr>
    <tr>
        <td>Obrana</td>
        <td><?= $fightProperties->getDefense() ?></td>
        <td><span class="hint">(není ovlivněna výzbrojí)</span></td>
    </tr>
    <tr>
        <td>OČ <img class="line-sized" src="images/emojione/defense-number-1f6e1.png"></td>
        <td><?= $fightProperties->getDefenseNumber() ?></td>
        <td><span class="hint">(ovlivněno pouze akcí, oslněním a Převahou)</span></td>
    </tr>
    <tr>
        <td>Zbroj <img class="line-sized" src="images/armor-icon.png"></td>
        <td><?= Tables::getIt()->getBodyArmorsTable()->getProtectionOf($controller->getSelectedBodyArmor()) ?></td>
        <td></td>
    </tr>
    <tr>
        <td>Helma <img class="line-sized" src="images/helm-icon.png"></td>
        <td><?= Tables::getIt()->getHelmsTable()->getProtectionOf($controller->getSelectedHelm()) ?></td>
        <td></td>
    </tr>
    </tbody>
</table>