<?php
namespace DrdPlus\Calculator\Fight;

/** @var Controller $controller */
?>
<h2 id="Obecně" class="row"><a class="inner" href="#Obecně">Obecně</a></h2>
<?php
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

$fightProperties = $controller->getFight()->getGenericFightProperties();
$previousFightProperties = $controller->getFight()->getPreviousGenericFightProperties();
$basicFightProperties = [];
$basicFightProperties[] = ['Boj', $fightProperties->getFight(), $controller->getClassForChangedValue($previousFightProperties->getFight(), $fightProperties->getFight()), '(není ovlivněn výzbrojí)'];
$basicFightProperties[] = ['Útok', $fightProperties->getAttack(), $controller->getClassForChangedValue($previousFightProperties->getAttack(), $fightProperties->getAttack()), '(není ovlivněn výzbrojí)'];
$basicFightProperties[] = ['Obrana', $fightProperties->getDefense(), $controller->getClassForChangedValue($previousFightProperties->getDefense(), $fightProperties->getDefense()), '(není ovlivněna výzbrojí)'];
$basicFightProperties[] = ['Střelba', $fightProperties->getShooting(), $controller->getClassForChangedValue($previousFightProperties->getShooting(), $fightProperties->getShooting()), '(není ovlivněna výzbrojí)'];
$basicFightProperties[] = ['OČ <img class="line-sized" src="images/emojione/defense-number-1f6e1.png">', $fightProperties->getDefenseNumber(), $controller->getClassForChangedValue($previousFightProperties->getDefenseNumber(), $fightProperties->getDefenseNumber()), '(ovlivněno pouze akcí, oslněním a Převahou)'];
$targetDistance = new Distance(1, Distance::METER, Tables::getIt()->getDistanceTable());
$attackNumber = $fightProperties->getAttackNumber($targetDistance, Size::getIt(1));
$basicFightProperties[] = ['ÚČ <img class="line-sized" src="images/emojione/defense-number-1f6e1.png">', $attackNumber, $controller->getClassForChangedValue($previousFightProperties->getAttackNumber($targetDistance, Size::getIt(1)), $attackNumber), ''];
$basicFightProperties[] = ['Zbroj <img class="line-sized" src="images/armor-icon.png">', $controller->getFight()->getProtectionOfCurrentBodyArmor(), $controller->getClassForChangedValue($controller->getFight()->getPreviousArmaments()->getProtectionOfPreviousBodyArmor(), $controller->getFight()->getProtectionOfCurrentBodyArmor()), ''];
$basicFightProperties[] = ['Helma <img class="line-sized" src="images/helm-icon.png">', $controller->getFight()->getCurrentHelmProtection(), $controller->getClassForChangedValue($controller->getFight()->getPreviousArmaments()->getPreviousHelmProtection(), $controller->getFight()->getCurrentHelmProtection()), ''];
$basicFightPropertyOrder = 1; ?>
<div class="row">
    <?php foreach ($basicFightProperties as [$name, $value, $class, $note]) {
        if ($basicFightPropertyOrder > 1 && $basicFightPropertyOrder % 2 === 1) { ?>
          <div class="row">
        <?php } ?>
      <div class="col-sm-6">
        <div class="row">
          <div class="col-sm-3"><?= $name ?></div>
          <div class="col-xs-3">
            <strong class="<?= $class ?>"><?= $controller->formatNumber($value) ?></strong>
              <?php if ($note) { ?>
                <span class="note"><?= $note ?></span>
              <?php } ?>
          </div>
        </div>
      </div>
        <?php if ($basicFightPropertyOrder % 2 === 0) { ?>
        </div>
        <?php }
        $basicFightPropertyOrder++;
    } ?>
</div>