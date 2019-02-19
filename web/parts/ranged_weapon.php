<?php
namespace DrdPlus\FightCalculator;

/** @var \DrdPlus\AttackSkeleton\Web\RangedWeaponBody $rangedWeaponBody */
/** @var \DrdPlus\FightCalculator\Web\RangedWeaponSkillBody $rangedWeaponSkillBody */
/** @var \DrdPlus\FightCalculator\Web\FightPropertiesBody $rangedWeaponFightPropertiesBody */
/** @var \DrdPlus\FightCalculator\Web\RangedTargetBody $rangedTargetBody */
?>
<div class="row">
  <h2 id="Na dálku" class="col"><a href="#Na dálku" class="inner">Na dálku</a></h2>
</div>
<fieldset>
    <?= $rangedWeaponBody->getValue() ?>
    <?= $rangedWeaponSkillBody->getValue() ?>
  <div class="with-skill-ranks row">
      <?= $rangedWeaponFightPropertiesBody->getValue() ?>
  </div>
  <div class="row"><?= $rangedTargetBody->getValue() ?></div>
</fieldset>