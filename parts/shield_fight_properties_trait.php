<?php
namespace DrdPlus\Calculator\Fight;

use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\Units\DistanceUnitCode;
use DrdPlus\FightProperties\FightProperties;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

/**
 * @var Controller $controller
 * @var FightProperties $previousShieldFightProperties
 * @var FightProperties $currentShieldFightProperties
 * @var ItemHoldingCode $previousShieldHolding
 * @var ItemHoldingCode $currentShieldHolding
 */

$previousAttackNumber = $previousShieldFightProperties->getAttackNumber(
    new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
    Size::getIt(0)
);
$currentAttackNumber = $currentShieldFightProperties->getAttackNumber(
    new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
    Size::getIt(0)
);
?>
<div class="col">
  BČ
  <img class="line-sized" src="images/emojione/fight-2694.png">
  <span class="<?= $controller->getClassForChangedValue($previousShieldFightProperties->getFightNumber(), $currentShieldFightProperties->getFightNumber()) ?>">
      <?= $controller->formatNumber($currentShieldFightProperties->getFightNumber()) ?>
  </span>
  <span class="hint">se štítem <a href="https://pph.drdplus.info/#boj_se_zbrani">jako zbraň</a></span>
</div>
<div class="col">
  ÚČ
  <img class="line-sized" src="images/emojione/fight-number-1f624.png">
  <span class="<?= $controller->getClassForChangedValue($previousAttackNumber, $currentAttackNumber) ?>">
      <?= $controller->formatNumber($currentAttackNumber) ?></span>
  <span class="hint">se štítem <a href="https://pph.drdplus.info/#boj_se_zbrani">jako zbraň</a></span>
</div>
<div class="col">
  ZZ
  <img class="line-sized" src="images/emojione/base-of-wounds-1f480.png">
  <span class="<?= $controller->getClassForChangedValue($previousShieldFightProperties->getBaseOfWounds(), $currentShieldFightProperties->getBaseOfWounds()) ?>">
      <?= $controller->formatNumber($currentShieldFightProperties->getBaseOfWounds()) ?></span>
  <span class="hint">se štítem <a href="https://pph.drdplus.info/#boj_se_zbrani">jako zbraň</a></span>
</div>
<div class="col">
  OČ
  <img class="line-sized" src="images/emojione/defense-number-1f6e1.png">
  <span class="<?= $controller->getClassForChangedValue($previousShieldFightProperties->getDefenseNumberWithShield(), $currentShieldFightProperties->getDefenseNumberWithShield()) ?>">
      <?= $controller->formatNumber($currentShieldFightProperties->getDefenseNumberWithShield()) ?>
</div>
<div class="col note">
  držen
  <span class="keyword <?php if ($previousShieldHolding->getValue() !== $currentShieldHolding->getValue()) { ?> changed <?php } ?>">
      <?= $currentShieldHolding->translateTo('cs') ?>
  </span>
</div>
