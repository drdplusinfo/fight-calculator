<?php /** @var \DrdPlus\FightCalculator\Web\FightWebPartsContainer $webPartsContainer */ ?>
<div class="row">
  <div class="col">
    <h2 id="Prostředí"><a href="#Prostředí" class="inner">Prostředí</a></h2>
    <fieldset>
      <div class="row">
          <?= $webPartsContainer->getRideBody()->getValue() ?>
      </div>
      <div class="row">
          <?= $webPartsContainer->getAnimalEnemyBody()->getValue() ?>
      </div>
      <div class="row"><input class="col" type="submit" value="Přepočítat"></div>
    </fieldset>
  </div>
</div>
