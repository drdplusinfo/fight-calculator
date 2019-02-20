<?php /** @var \DrdPlus\FightCalculator\Web\FightWebPartsContainer $webPartsContainer */ ?>
<div class="row">
  <h2 id="Vlastnosti" class="col"><a href="#Vlastnosti" class="inner">Vlastnosti</a></h2>
</div>
<div class="row">
  <div class="col-sm">
      <?= $webPartsContainer->getProfessionsBody()->getValue() ?>
  </div>
</div>
<fieldset class="row">
  <div class="col">
      <?= $webPartsContainer->getBodyPropertiesBody()->getValue() ?>
  </div>
</fieldset>