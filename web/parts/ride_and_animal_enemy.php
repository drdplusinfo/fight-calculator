<?php /** @var \DrdPlus\FightCalculator\Web\FightWebPartsContainer $webPartsContainer */ ?>
<div class="col">
  <h2 id="Prostředí"><a href="#Prostředí" class="inner">Prostředí</a></h2>
  <fieldset>
    <div class="row">
      <div class="col">
          <?= $webPartsContainer->getRideBody()->getValue() ?>
      </div>
    </div>
    <div class="row">
        <?= $webPartsContainer->getAnimalEnemyBody()->getValue() ?>
    </div>
    <div class="row"><input class="col" type="submit" value="Přepočítat"></div>
  </fieldset>
</div>
