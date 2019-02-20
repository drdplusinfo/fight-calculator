<?php /** @var \DrdPlus\FightCalculator\Web\FightWebPartsContainer $webPartsContainer */ ?>
<div class="row">
  <h2 id="Na blízko" class="col"><a href="#Na blízko" class="inner">Na blízko</a></h2>
</div>
<fieldset>
    <?= $webPartsContainer->getMeleeWeaponBody()->getValue() ?>
    <?= $webPartsContainer->getMeleeWeaponSkillBody()->getValue() ?>
  <div class="with-skill-ranks row">
      <?= $webPartsContainer->getMeleeWeaponFightPropertiesBody()->getValue() ?>
  </div>
</fieldset>