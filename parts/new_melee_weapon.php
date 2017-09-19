<?php

use DrdPlus\Codes\Body\WoundTypeCode;

/** @var \DrdPlus\Fight\Controller $controller */

?>
<label>Název <input type="text" name="<?= $controller::NEW_MELEE_WEAPON_NAME ?>"
                    <?php if ($controller->addingNewMeleeWeapon()) { ?>required<?php } ?>></label>
<label>Potřebná síla <input type="number" min="-20" max="50"
                            name="<?= $controller::NEW_MELEE_WEAPON_REQUIRED_STRENGTH ?>"
                            <?php if ($controller->addingNewMeleeWeapon()) { ?>required<?php } ?>></label>
<label>Délka <input type="number" min="0" max="10" name="<?= $controller::NEW_MELEE_WEAPON_LENGTH ?>"
                    <?php if ($controller->addingNewMeleeWeapon()) { ?>required<?php } ?>></label>
<label>Útočnost <input type="number" min="=-20" max="50" name="<?= $controller::NEW_MELEE_WEAPON_ATTACK ?>"
                       <?php if ($controller->addingNewMeleeWeapon()) { ?>required<?php } ?>></label>
<label>Zranění <input type="number" min="=-20" max="50" name="<?= $controller::NEW_MELEE_WEAPON_WOUNDS ?>"
                      <?php if ($controller->addingNewMeleeWeapon()) { ?>required<?php } ?>></label>
<label>Typ <select name="<?= $controller::NEW_MELEE_WEAPON_WOUND_TYPE ?>"
                   <?php if ($controller->addingNewMeleeWeapon()) { ?>required<?php } ?>>
        <?php foreach (WoundTypeCode::getPossibleValues() as $woundTypeValue) {
            $woundType = WoundTypeCode::getIt($woundTypeValue); ?>
            <option value="<?= $woundTypeValue ?>"><?= $woundType->translateTo('cs') ?></option>
        <?php } ?>
    </select>
</label>
<input type="submit" value="Přidat">
<a class="button cancel" id="cancelNewMeleeWeapon"
   href="<?= $controller->getCurrentUrlWithQuery([$controller::ACTION => '']); ?>">Zrušit</a>