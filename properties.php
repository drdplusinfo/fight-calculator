<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\ProfessionCode;

/** @var Controller $controller */
?>
<div class="panel">
    <h2 id="Vlastnosti"><a href="#Vlastnosti" class="inner">Vlastnosti</a></h2>
    <div class="panel">
        <label>Povolání <select name="<?= $controller::PROFESSION ?>">
                <?php foreach (ProfessionCode::getPossibleValues() as $professionValue) {
                    ?>
                    <option value="<?= $professionValue ?>"
                            <?php if ($controller->getSelectedProfessionCode()->getValue() === $professionValue) { ?>selected<?php } ?>>
                        <?= ProfessionCode::getIt($professionValue)->translateTo('cs') ?>
                    </option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div class="panel"><label>Síla <input type="number" name="<?= $controller::STRENGTH ?>" min="-40" max="40"
                                          value="<?= $controller->getSelectedStrength()->getValue() ?>"></label>
    </div>
    <div class="panel"><label>Obratnost <input type="number" name="<?= $controller::AGILITY ?>" min="-40" max="40"
                                               value="<?= $controller->getSelectedAgility()->getValue() ?>"></label>
    </div>
    <div class="panel"><label>Zručnost <input type="number" name="<?= $controller::KNACK ?>" min="-40" max="40"
                                              value="<?= $controller->getSelectedKnack()->getValue() ?>"></label>
    </div>
    <div class="panel"><label>Vůle <input type="number" name="<?= $controller::WILL ?>" min="-40" max="40"
                                          value="<?= $controller->getSelectedWill()->getValue() ?>"></label></div>
    <div class="panel"><label>Inteligence <input type="number" name="<?= $controller::INTELLIGENCE ?>" min="-40"
                                                 max="40"
                                                 value="<?= $controller->getSelectedIntelligence()->getValue() ?>"></label>
    </div>
    <div class="panel"><label>Charisma <input type="number" name="<?= $controller::CHARISMA ?>" min="-40" max="40"
                                              value="<?= $controller->getSelectedCharisma()->getValue() ?>"></label>
    </div>
    <div class="panel"><label>Výška v cm <input type="number" name="<?= $controller::HEIGHT_IN_CM ?>" min="110"
                                                max="290"
                                                value="<?= $controller->getSelectedHeightInCm()->getValue() ?>"></label>
    </div>
    <div class="panel"><label>Velikost <input type="number" name="<?= $controller::SIZE ?>" min="-10" max="10"
                                              value="<?= $controller->getSelectedSize()->getValue() ?>"></label>
    </div>
    <div class="block"><input type="submit" value="Přepočítat"></div>
</div>
