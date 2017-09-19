<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\ProfessionCode;

/** @var Controller $controller */
?>
<div class="panel">
    <label class="block" for="profession">Povolání</label>
    <select class="block" id="profession" name="<?= $controller::PROFESSION ?>">
        <?php foreach (ProfessionCode::getPossibleValues() as $professionValue) { ?>
            <option value="<?= $professionValue ?>"
                    <?php if ($controller->getSelectedProfessionCode()->getValue() === $professionValue) { ?>selected<?php } ?>>
                <?= ProfessionCode::getIt($professionValue)->translateTo('cs') ?>
            </option>
        <?php } ?>
    </select>
</div>
<div class="panel body-properties">
    <div class="panel">
        <div class="block"><label for="strength">Síla</label></div>
        <div class="block"><input id="strength" type="number" name="<?= $controller::STRENGTH ?>" min="-40" max="40"
                                  value="<?= $controller->getCurrentProperties()->getCurrentStrength()->getValue() ?>">
        </div>
    </div>
    <div class="panel">
        <div class="block"><label for="agility">Obratnost</label></div>
        <div class="block"><input id="agility" type="number" name="<?= $controller::AGILITY ?>" min="-40" max="40"
                                  value="<?= $controller->getCurrentProperties()->getCurrentAgility()->getValue() ?>">
        </div>
    </div>
    <div class="panel">
        <div class="block"><label for="knack">Zručnost</label></div>
        <div class="block"><input id="knack" type="number" name="<?= $controller::KNACK ?>" min="-40" max="40"
                                  value="<?= $controller->getCurrentProperties()->getCurrentKnack()->getValue() ?>">
        </div>
    </div>
    <div class="panel">
        <div class="block"><label for="will">Vůle</label></div>
        <div class="block"><input id="will" type="number" name="<?= $controller::WILL ?>" min="-40" max="40"
                                  value="<?= $controller->getCurrentProperties()->getCurrentWill()->getValue() ?>">
        </div>
    </div>
    <div class="panel">
        <div class="block"><label for="intelligence">Inteligence</label></div>
        <div class="block">
            <input id="intelligence" type="number" name="<?= $controller::INTELLIGENCE ?>" min="-40" max="40"
                   value="<?= $controller->getCurrentProperties()->getCurrentIntelligence()->getValue() ?>">
        </div>
    </div>
    <div class="panel">
        <div class="block"><label for="charisma">Charisma</label></div>
        <div class="block">
            <input id="charisma" type="number" name="<?= $controller::CHARISMA ?>" min="-40" max="40"
                   value="<?= $controller->getCurrentProperties()->getCurrentCharisma()->getValue() ?>"></div>
    </div>
    <div class="panel">
        <div class="block"><label for="height">Výška v cm</label></div>
        <div class="block">
            <input id="height" type="number" name="<?= $controller::HEIGHT_IN_CM ?>" min="110"
                   max="290"
                   value="<?= $controller->getCurrentProperties()->getCurrentHeightInCm()->getValue() ?>">
        </div>
    </div>
    <div class="panel">
        <div class="block"><label for="size">Velikost</label></div>
        <div class="block"><input id="size" type="number" name="<?= $controller::SIZE ?>" min="-10" max="10"
                                  value="<?= $controller->getCurrentProperties()->getCurrentSize()->getValue() ?>">
        </div>
    </div>
</div>
<div class="block"><input type="submit" value="Přepočítat"></div>
