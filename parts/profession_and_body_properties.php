<?php
namespace DrdPlus\FightCalculator;

use DrdPlus\Codes\ProfessionCode;

/** @var FightController $controller */
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
      <select id="profession" name="<?= FightController::PROFESSION ?>">
          <?php foreach (ProfessionCode::getPossibleValues() as $professionValue) { ?>
            <option value="<?= $professionValue ?>"
                    <?php if ($controller->getFight()->getCurrentProfessionCode()->getValue() === $professionValue) { ?>selected<?php } ?>>
                <?= ProfessionCode::getIt($professionValue)->translateTo('cs') ?>
            </option>
          <?php } ?>
      </select>
    </div>
  </div>
    <?= $controller->getBodyPropertiesContent() ?>
</fieldset>