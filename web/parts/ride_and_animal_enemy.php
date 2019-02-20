<?php
/** @var \DrdPlus\FightCalculator\Web\RideBody $rideBody */
/** @var \DrdPlus\FightCalculator\Web\AnimalEnemyBody $animalEnemyBody */
?>
<div class="col">
  <h2 id="Prostředí"><a href="#Prostředí" class="inner">Prostředí</a></h2>
  <fieldset>
    <div class="row">
      <div class="col">
          <?= $rideBody->getValue() ?>
      </div>
    </div>
    <div class="row">
        <?= $animalEnemyBody->getValue() ?>
    </div>
    <div class="row"><input class="col" type="submit" value="Přepočítat"></div>
  </fieldset>
</div>
