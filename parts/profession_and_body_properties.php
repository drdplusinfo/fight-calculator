<?php
namespace DrdPlus\FightCalculator;

use DrdPlus\Codes\ProfessionCode;

/** @var FightController $controller */
?>
<div class="row">
  <h2 id="Vlastnosti" class="col"><a href="#Vlastnosti" class="inner">Vlastnosti</a></h2>
</div>
<fieldset class="row">
  <div class="col-sm-2">
    <label for="profession">Povolání</label>
    <select id="profession" name="<?= FightController::PROFESSION ?>">
        <?php foreach (ProfessionCode::getPossibleValues() as $professionValue) { ?>
          <option value="<?= $professionValue ?>"
                  <?php if ($controller->getFight()->getCurrentProfessionCode()->getValue() === $professionValue) { ?>selected<?php } ?>>
              <?= ProfessionCode::getIt($professionValue)->translateTo('cs') ?>
          </option>
        <?php } ?>
    </select>
  </div>
  <div class="col body-properties">
    <div class="row">
      <div class="col">
        <label for="strength">Síla</label>
        <input id="strength" type="number" name="<?= FightController::STRENGTH ?>" min="-40" max="40" step="1"
               value="<?= $controller->getCurrentProperties()->getCurrentStrength()->getValue() ?>">
      </div>
      <div class="col">
        <div><label for="agility">Obratnost</label></div>
        <div><input id="agility" type="number" name="<?= FightController::AGILITY ?>" min="-40" max="40" step="1"
                    value="<?= $controller->getCurrentProperties()->getCurrentAgility()->getValue() ?>">
        </div>
      </div>
      <div class="col">
        <div><label for="knack">Zručnost</label></div>
        <div><input id="knack" type="number" name="<?= FightController::KNACK ?>" min="-40" max="40" step="1"
                    value="<?= $controller->getCurrentProperties()->getCurrentKnack()->getValue() ?>">
        </div>
      </div>
      <div class="col">
        <div><label for="will">Vůle</label></div>
        <div><input id="will" type="number" name="<?= FightController::WILL ?>" min="-40" max="40" step="1"
                    value="<?= $controller->getCurrentProperties()->getCurrentWill()->getValue() ?>">
        </div>
      </div>
      <div class="col">
        <div><label for="intelligence">Inteligence</label></div>
        <div>
          <input id="intelligence" type="number" name="<?= FightController::INTELLIGENCE ?>" min="-40" max="40" step="1"
                 value="<?= $controller->getCurrentProperties()->getCurrentIntelligence()->getValue() ?>">
        </div>
      </div>
      <div class="col">
        <div><label for="charisma">Charisma</label></div>
        <div>
          <input id="charisma" type="number" name="<?= FightController::CHARISMA ?>" min="-40" max="40" step="1"
                 value="<?= $controller->getCurrentProperties()->getCurrentCharisma()->getValue() ?>"></div>
      </div>
      <div class="col">
        <div><label for="height">Výška <span class="note">v cm</span></label></div>
        <div>
          <input id="height" type="number" name="<?= FightController::HEIGHT_IN_CM ?>" min="110"
                 max="290"
                 value="<?= $controller->getCurrentProperties()->getCurrentHeightInCm()->getValue() ?>">
        </div>
      </div>
      <div class="col">
        <div><label for="size">Velikost</label></div>
        <div><input id="size" type="number" name="<?= FightController::SIZE ?>" min="-10" max="10" step="1"
                    value="<?= $controller->getCurrentProperties()->getCurrentSize()->getValue() ?>">
        </div>
      </div>
    </div>
  </div>
</fieldset>