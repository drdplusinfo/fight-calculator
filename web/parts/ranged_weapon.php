<?php
namespace DrdPlus\FightCalculator;

/** @var \DrdPlus\AttackSkeleton\Web\RangedWeaponBody $rangedWeaponBody */
/** @var \DrdPlus\FightCalculator\Web\RangedWeaponSkillBody $rangedWeaponSkillBody */
/** @var \DrdPlus\FightCalculator\Web\FightPropertiesBody $rangedFightPropertiesBody */
/** @var \DrdPlus\FightCalculator\Web\RangedTarget $rangedTarget */
?>
<div class="row">
  <h2 id="Na dálku" class="col"><a href="#Na dálku" class="inner">Na dálku</a></h2>
</div>
<fieldset>
    <?= $rangedWeaponBody->getValue() ?>
    <?= $rangedWeaponSkillBody->getValue() ?>
  <div class="with-skill-ranks row">
      <?= $rangedFightPropertiesBody->getValue() ?>
  </div>
  <div class="row"><?= $rangedTarget->getValue() ?></div>
</fieldset>