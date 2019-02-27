<?php /** @var \DrdPlus\FightCalculator\Web\FightWebPartsContainer $webPartsContainer */ ?>
<div class="row">
  <h2 id="Štít" class="col"><a href="#Štít" class="inner">Štít</a></h2>
</div>
<fieldset>
    <?= $webPartsContainer->getShieldBody()->getValue() ?>
  <div class="row">
    <div class="col">
        <?= $webPartsContainer->getShieldUsageSkillBody()->getValue(); ?>
    </div>
    <div class="col">
        <?= $webPartsContainer->getFightWithShieldSkillBody()->getValue() ?>
    </div>
  </div>
  <div class="row <?php if ($webPartsContainer->isWithoutShield()): ?>hidden<?php endif; ?>">
    <div class="col">
      <div class="row">
        <h4 class="col">štít jako zbraň se zbraní na blízko</h4>
      </div>
      <?= $webPartsContainer->getShieldWithMeleeWeaponBody()->getValue() ?>
    </div>
    <div class="col">
      <div class="row">
        <h4 class="col">štít jako zbraň se zbraní na dálku</h4>
      </div>
      <?= $webPartsContainer->getShieldWithRangedWeaponBody()->getValue() ?>
    </div>
  </div>
</fieldset>