<?php
namespace DrdPlus\FightCalculator;

/** @var \DrdPlus\AttackSkeleton\Web\MeleeWeaponBody $meleeWeaponBody */
/** @var \DrdPlus\FightCalculator\Web\MeleeWeaponSkillBody $meleeWeaponSkillBody */
/** @var \DrdPlus\FightCalculator\Web\FightPropertiesBody $meleeWeaponFightPropertiesBody */
?>
<div class="row">
  <h2 id="Na blízko" class="col"><a href="#Na blízko" class="inner">Na blízko</a></h2>
</div>
<fieldset>
    <?= $meleeWeaponBody->getValue() ?>
    <?= $meleeWeaponSkillBody->getValue() ?>
  <div class="with-skill-ranks row">
      <?= $meleeWeaponFightPropertiesBody->getValue() ?>
  </div>
</fieldset>