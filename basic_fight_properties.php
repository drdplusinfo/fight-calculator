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
        <td <?php if ($previousFightProperties->getFight()->getValue() !== $fightProperties->getFight()->getValue()) { ?>
            class="changed" <?php } ?>>
            <?= $fightProperties->getFight() ?>
        </td>
        <td><span class="hint">(není ovlivněn výzbrojí)</span></td>
    </tr>
    <tr>
        <td>Útok</td>
        <td></td>
        <td <?php if ($previousFightProperties->getAttack()->getValue() !== $fightProperties->getAttack()->getValue()) { ?>
            class="changed" <?php } ?>>
            <?= $fightProperties->getAttack() ?>
        </td>
        <td><span class="hint">(není ovlivněn výzbrojí)</span></td>
    </tr>
    <tr>
        <td>Obrana</td>
        <td></td>
        <td <?php if ($previousFightProperties->getDefense()->getValue() !== $fightProperties->getDefense()->getValue()) { ?>
            class="changed" <?php } ?>>
            <?= $fightProperties->getDefense() ?>
        </td>
        <td><span class="hint">(není ovlivněna výzbrojí)</span></td>
    </tr>
    <tr>
        <td>OČ</td>
        <td><img class="line-sized" src="images/emojione/defense-number-1f6e1.png"></td>
        <td <?php if ($previousFightProperties->getDefenseNumber()->getValue() !== $fightProperties->getDefenseNumber()->getValue()) { ?>
            class="changed" <?php } ?>>
            <?= $fightProperties->getDefenseNumber() ?>
        </td>
        <td><span class="hint">(ovlivněno pouze akcí, oslněním a Převahou)</span></td>
    </tr>
    <tr>
        <td>Zbroj</td>
        <td><img class="line-sized" src="images/armor-icon.png"></td>
        <td><?= $controller->getProtectionOfSelectedBodyArmor() ?></td>
        <td></td>
    </tr>
    <tr>
        <td>Helma</td>
        <td class="line-sized"><img src="images/helm-icon.png"></td>
        <td <?php if ($controller->getProtectionOfPreviousHelm() !== $controller->getProtectionOfSelectedHelm()) { ?>
            class="changed" <?php } ?>>
            <?= $controller->getProtectionOfSelectedHelm() ?>
        </td>
        <td></td>
    </tr>
    </tbody>
</table>