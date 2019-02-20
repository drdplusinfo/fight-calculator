<?php /** @var \DrdPlus\FightCalculator\Web\FightWebPartsContainer $webPartsContainer */ ?>
<div class="row">
  <h2 id="Na dálku" class="col"><a href="#Na dálku" class="inner">Na dálku</a></h2>
</div>
<fieldset>
    <?= $webPartsContainer->getRangedWeaponBody()->getValue() ?>
    <?= $webPartsContainer->getRangedWeaponSkillBody()->getValue() ?>
  <div class="with-skill-ranks row">
      <?= $webPartsContainer->getRangedWeaponFightPropertiesBody()->getValue() ?>
  </div>
  <div class="row"><?= $webPartsContainer->getRangedTargetBody()->getValue() ?></div>
</fieldset>