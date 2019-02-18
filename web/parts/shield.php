<?php
namespace DrdPlus\FightCalculator;

/** @var \DrdPlus\AttackSkeleton\Web\ShieldBody $shieldBody */
/** @var \DrdPlus\FightCalculator\Web\ShieldUsageSkillBody $shieldUsageSkillBody */
/** @var \DrdPlus\FightCalculator\Web\FightWithShieldSkillBody $fightWithShieldSkillBody */
?>

<div class="row">
  <h2 id="Štít" class="col"><a href="#Štít" class="inner">Štít</a></h2>
</div>
<fieldset>
    <?= $shieldBody->getValue() ?>
  <div class="row">
    <div class="col">
        <?= $shieldUsageSkillBody->getValue(); ?>
    </div>
    <div class="col">
        <?= $fightWithShieldSkillBody->getValue() ?>
    </div>
  </div>
  <div class="row <?php if ($controller->getFight()->getCurrentShield()->isUnarmed()) { ?>hidden<?php } ?>">
    <div class="col">
        <?php
        /** @noinspection PhpUnusedLocalVariableInspection */
        $currentShieldFightProperties = $controller->getFight()->getCurrentMeleeShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousShieldFightProperties = $controller->getFight()->getPreviousMeleeShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $currentShieldHolding = $controller->getFight()->getCurrentMeleeShieldHolding();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousShieldHolding = $controller->getFight()->getPreviousArmaments()->getPreviousMeleeShieldHolding();
        ?>
      <div class="row">
        <h4 class="col">štít se zbraní na blízko</h4>
      </div>
      <div class="row">
          <?php include __DIR__ . '/shield_fight_properties_trait.php' ?>
      </div>
    </div>
    <div class="col">
        <?php
        /** @noinspection PhpUnusedLocalVariableInspection */
        $currentShieldFightProperties = $controller->getFight()->getRangedShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousShieldFightProperties = $controller->getFight()->getPreviousRangedShieldFightProperties();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $currentShieldHolding = $controller->getFight()->getCurrentRangedShieldHolding();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $previousShieldHolding = $controller->getFight()->getPreviousArmaments()->getPreviousRangedShieldHolding();
        ?>
      <div class="row">
        <div class="col"><h4>štít se zbraní na dálku</h4></div>
      </div>
      <div class="row">
          <?php include __DIR__ . '/shield_fight_properties_trait.php' ?>
      </div>
    </div>
</fieldset>