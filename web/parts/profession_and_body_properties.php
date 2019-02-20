<?php /** @var \DrdPlus\FightCalculator\Web\FightWebPartsContainer $webPartsContainer */ ?>
<div class="row">
  <h2 id="Vlastnosti" class="col"><a href="#Vlastnosti" class="inner">Vlastnosti</a></h2>
</div>
<fieldset class="row body-properties">
  <div class="col-sm-2">
    <div>
      <label for="profession">Povolání</label>
    </div>
    <div>
        <?= $webPartsContainer->getProfessionsBody()->getValue() ?>
    </div>
  </div>
    <?= $webPartsContainer->getBodyPropertiesBody()->getValue() ?>
</fieldset>