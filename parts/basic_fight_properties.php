<?php
namespace DrdPlus\Fight;

/** @var Controller $controller */
?>
<h2 id="Obecně"><a class="inner" href="#Obecně">Obecně</a></h2>
<table class="block result shortened">
    <?php
    $fightProperties = $controller->getGenericFightProperties();
    $previousFightProperties = $controller->getPreviousGenericFightProperties();
    ?>
    <tbody>
    <tr>
        <td>Boj</td>
        <td></td>
        <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getFight(), $fightProperties->getFight()) ?>">
            <?= $fightProperties->getFight() ?>
        </td>
        <td><span class="hint">(není ovlivněn výzbrojí)</span></td>
    </tr>
    <tr>
        <td>Útok</td>
        <td></td>
        <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getAttack(), $fightProperties->getAttack()) ?>">
            <?= $fightProperties->getAttack() ?>
        </td>
        <td><span class="hint">(není ovlivněn výzbrojí)</span></td>
    </tr>
    <tr>
        <td>Střelba</td>
        <td></td>
        <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getShooting(), $fightProperties->getShooting()) ?>">
            <?= $fightProperties->getShooting() ?>
        </td>
        <td><span class="hint">(není ovlivněna výzbrojí)</span></td>
    </tr>
    <tr>
        <td>Obrana</td>
        <td></td>
        <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getDefense(), $fightProperties->getDefense()) ?>">
            <?= $fightProperties->getDefense() ?>
        </td>
        <td><span class="hint">(není ovlivněna výzbrojí)</span></td>
    </tr>
    <tr>
        <td>OČ</td>
        <td><img class="line-sized" src="images/emojione/defense-number-1f6e1.png"></td>
        <td class="<?= $controller->getClassForChangedValue($previousFightProperties->getDefenseNumber(), $fightProperties->getDefenseNumber()) ?>">
            <?= $fightProperties->getDefenseNumber() ?>
        </td>
        <td><span class="hint">(ovlivněno pouze akcí, oslněním a Převahou)</span></td>
    </tr>
    <tr>
        <td>Zbroj</td>
        <td><img class="line-sized" src="images/armor-icon.png"></td>
        <td class="<?= $controller->getClassForChangedValue($controller->getProtectionOfPreviousBodyArmor(), $controller->getProtectionOfSelectedBodyArmor()) ?>">
            <?= $controller->getProtectionOfSelectedBodyArmor() ?>
        </td>
        <td></td>
    </tr>
    <tr>
        <td>Helma</td>
        <td><img class="line-sized" src="images/helm-icon.png"></td>
        <td class="<?= $controller->getClassForChangedValue($controller->getPreviousHelmProtection(), $controller->getSelectedHelmProtection()) ?>">
            <?= $controller->getSelectedHelmProtection() ?>
        </td>
        <td></td>
    </tr>
    </tbody>
</table>