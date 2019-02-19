<?php
namespace DrdPlus\FightCalculator;

/** @var \DrdPlus\AttackSkeleton\Web\ShieldBody $shieldBody */
/** @var \DrdPlus\FightCalculator\Web\ShieldUsageSkillBody $shieldUsageSkillBody */
/** @var \DrdPlus\FightCalculator\Web\FightWithShieldSkillBody $fightWithShieldSkillBody */
/** @var bool $withoutShield */
/** @var \DrdPlus\FightCalculator\Web\ShieldFightPropertiesBody $shieldWithMeleeWeaponBody */
/** @var \DrdPlus\FightCalculator\Web\ShieldFightPropertiesBody $shieldWithRangedWeaponBody */

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
  <div class="row <?php if ($withoutShield): ?>hidden<?php endif; ?>">
    <div class="col">
      <div class="row">
        <h4 class="col">štít se zbraní na blízko</h4>
      </div>
      <div class="row">
          <?= $shieldWithMeleeWeaponBody->getValue() ?>
      </div>
    </div>
    <div class="col">
      <div class="row">
        <div class="col"><h4>štít se zbraní na dálku</h4></div>
      </div>
      <div class="row">
          <?= $shieldWithRangedWeaponBody->getValue() ?>
      </div>
    </div>
</fieldset>