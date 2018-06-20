<?php
namespace DrdPlus\FightCalculator;

use DrdPlus\FightProperties\FightProperties;

/**
 * @var FightController $controller
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
<div class="col">
  BČ
  <img class="line-sized" src="/images/emojione/fight-2694.png">
  <span class="<?= $controller->getClassForChangedValue($previousFightProperties->getFightNumber(), $fightProperties->getFightNumber()) ?>">
        <?= $fightProperties->getFightNumber() ?>

</div>
<div class="col">
  ÚČ
  <img class="line-sized" src="/images/emojione/fight-number-1f624.png">
  <span class="<?= $controller->getClassForChangedValue($previousAttackNumber, $currentAttackNumber) ?>">
        <?= $currentAttackNumber ?>

</div>
<div class="col">
  ZZ
  <img class="line-sized" src="/images/emojione/base-of-wounds-1f480.png">
  <span class="<?= $controller->getClassForChangedValue($previousFightProperties->getBaseOfWounds(), $fightProperties->getBaseOfWounds()) ?>">
        <?= $fightProperties->getBaseOfWounds() ?>

</div>
<div class="col">
  OČ
  <img class="line-sized" src="/images/emojione/defense-number-1f6e1.png">
  <span class="<?= $controller->getClassForChangedValue($previousFightProperties->getDefenseNumberWithWeaponlike(), $fightProperties->getDefenseNumberWithWeaponlike()) ?>">
        <?= $fightProperties->getDefenseNumberWithWeaponlike() ?>

</div>