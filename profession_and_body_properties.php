<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\ProfessionCode;

/** @var Controller $controller */
?>
<div class="block"><h2 id="Vlastnosti"><a href="#Vlastnosti" class="inner">Vlastnosti</a></h2></div>
<div class="block">
    <label for="profession">Povolání</label>
    <select id="profession" name="<?= $controller::PROFESSION ?>">
        <?php foreach (ProfessionCode::getPossibleValues() as $professionValue) { ?>
            <option value="<?= $professionValue ?>"
                    <?php if ($controller->getSelectedProfessionCode()->getValue() === $professionValue) { ?>selected<?php } ?>>
                <?= ProfessionCode::getIt($professionValue)->translateTo('cs') ?>
            </option>
        <?php } ?>
    </select>
</div>
<div class="block body-properties">
    <div class="panel">
        <div class="block"><label for="strength">Síla</label></div>
        <div class="block"><input id="strength" type="number" name="<?= $controller::STRENGTH ?>" min="-40" max="40"
                                  value="<?= $controller->getSelectedStrength()->getValue() ?>"></div>
    </div>
    <div class="panel">
        <div class="block"><label for="agility">Obratnost</label></div>
        <div class="block"><input id="agility" type="number" name="<?= $controller::AGILITY ?>" min="-40" max="40"
                                  value="<?= $controller->getSelectedAgility()->getValue() ?>"></div>
    </div>
    <div class="panel">
        <div class="block"><label for="knack">Zručnost</label></div>
        <div class="block"><input id="knack" type="number" name="<?= $controller::KNACK ?>" min="-40" max="40"
                                  value="<?= $controller->getSelectedKnack()->getValue() ?>"></div>
    </div>
    <div class="panel">
        <div class="block"><label for="will">Vůle</label></div>
        <div class="block"><input id="will" type="number" name="<?= $controller::WILL ?>" min="-40" max="40"
                                  value="<?= $controller->getSelectedWill()->getValue() ?>"></div>
    </div>
    <div class="panel">
        <div class="block"><label for="intelligence">Inteligence</label></div>
        <div class="block">
            <input id="intelligence" type="number" name="<?= $controller::INTELLIGENCE ?>" min="-40" max="40"
                   value="<?= $controller->getSelectedIntelligence()->getValue() ?>">
        </div>
    </div>
    <div class="panel">
        <div class="block"><label for="charisma">Charisma</label></div>
        <div class="block">
            <input id="charisma" type="number" name="<?= $controller::CHARISMA ?>" min="-40" max="40"
                   value="<?= $controller->getSelectedCharisma()->getValue() ?>"></div>
    </div>
    <div class="panel">
        <div class="block"><label for="height">Výška v cm</label></div>
        <div class="block">
            <input id="height" type="number" name="<?= $controller::HEIGHT_IN_CM ?>" min="110"
                   max="290"
                   value="<?= $controller->getSelectedHeightInCm()->getValue() ?>"></div>
    </div>
    <div class="panel">
        <div class="block"><label for="size">Velikost</label></div>
        <div class="block"><input id="size" type="number" name="<?= $controller::SIZE ?>" min="-10" max="10"
                                  value="<?= $controller->getSelectedSize()->getValue() ?>"></div>
    </div>
</div>
<div class="block"><input type="submit" value="Přepočítat"></div>
