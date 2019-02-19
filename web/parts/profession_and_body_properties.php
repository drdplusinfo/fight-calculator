<?php
namespace DrdPlus\FightCalculator;

/** @var \DrdPlus\FightCalculator\Web\ProfessionsBody $professionsBody */
/** @var \DrdPlus\AttackSkeleton\Web\BodyPropertiesBody $bodyPropertiesBody */
?>
<div class="row">
  <h2 id="Vlastnosti" class="col"><a href="#Vlastnosti" class="inner">Vlastnosti</a></h2>
</div>
<fieldset class="row body-properties">
  <div class="col-sm-2">
    <div>
      <label for="profession">Povolání</label>
    </div>
    <div>
        <?= $professionsBody->getValue() ?>
    </div>
  </div>
    <?= $bodyPropertiesBody->getValue() ?>
</fieldset>