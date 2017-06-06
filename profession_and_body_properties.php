<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\ProfessionCode;

/** @var Controller $controller */
?>
<table class="panel">
    <thead>
    <tr>
        <th colspan="100%"><h2 id="Vlastnosti"><a href="#Vlastnosti" class="inner">Vlastnosti</a></h2></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <label for="profession">Povolání</label>
        </td>
        <td>
            <select id="profession" name="<?= $controller::PROFESSION ?>">
                <?php foreach (ProfessionCode::getPossibleValues() as $professionValue) { ?>
                    <option value="<?= $professionValue ?>"
                            <?php if ($controller->getSelectedProfessionCode()->getValue() === $professionValue) { ?>selected<?php } ?>>
                        <?= ProfessionCode::getIt($professionValue)->translateTo('cs') ?>
                    </option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label for="strength">Síla</label>
        </td>
        <td>
            <input id="strength" type="number" name="<?= $controller::STRENGTH ?>" min="-40" max="40"
                   value="<?= $controller->getSelectedStrength()->getValue() ?>">
        </td>
    </tr>
    <tr>
        <td><label for="agility">Obratnost</label></td>
        <td>
            <input id="agility" type="number" name="<?= $controller::AGILITY ?>" min="-40" max="40"
                   value="<?= $controller->getSelectedAgility()->getValue() ?>">
        </td>
    </tr>
    <tr>
        <td><label for="knack">Zručnost</label></td>
        <td><input id="knack" type="number" name="<?= $controller::KNACK ?>" min="-40" max="40"
                   value="<?= $controller->getSelectedKnack()->getValue() ?>"></td>
    </tr>
    <tr>
        <td><label for="will">Vůle</label></td>
        <td><input id="will" type="number" name="<?= $controller::WILL ?>" min="-40" max="40"
                   value="<?= $controller->getSelectedWill()->getValue() ?>"></td>
    </tr>
    <tr>
        <td><label for="intelligence">Inteligence</label></td>
        <td><input id="intelligence" type="number" name="<?= $controller::INTELLIGENCE ?>" min="-40"
                   max="40"
                   value="<?= $controller->getSelectedIntelligence()->getValue() ?>"></td>
    </tr>
    <tr>
        <td><label for="charisma">Charisma</label></td>
        <td><input id="charisma" type="number" name="<?= $controller::CHARISMA ?>" min="-40" max="40"
                   value="<?= $controller->getSelectedCharisma()->getValue() ?>"></td>
    </tr>
    <tr>
        <td><label for="height">Výška v cm</label></td>
        <td><input id="height" type="number" name="<?= $controller::HEIGHT_IN_CM ?>" min="110"
                   max="290"
                   value="<?= $controller->getSelectedHeightInCm()->getValue() ?>"></td>
    </tr>
    <tr>
        <td><label for="size">Velikost</label></td>
        <td><input id="size" type="number" name="<?= $controller::SIZE ?>" min="-10" max="10"
                   value="<?= $controller->getSelectedSize()->getValue() ?>"></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="100%">
            <div class="block"><input type="submit" value="Přepočítat"></div>
        </td>
    </tr>
    </tfoot>
</table>
