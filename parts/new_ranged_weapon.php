<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Body\WoundTypeCode;
use \DrdPlus\Codes\Armaments\WeaponCategoryCode;

/** @var \DrdPlus\Fight\Controller $controller */

?>
<label>Název <input type="text" name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_NAME ?>[0]"
                    required="required"></label>
<label>Kategorie <select name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_CATEGORY ?>[0]"
                         required="required">
        <?php foreach (WeaponCategoryCode::getRangedWeaponCategoryValues() as $rangedWeaponCategoryValue) {
            $weaponCategory = WeaponCategoryCode::getIt($rangedWeaponCategoryValue); ?>
            <option value="<?= $rangedWeaponCategoryValue ?>"><?= $weaponCategory->translateTo('cs') ?></option>
        <?php } ?>
    </select>
</label>
<label>Potřebná síla <input type="number" min="-20" max="50" value="0"
                            name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_REQUIRED_STRENGTH ?>[0]"
                            required="required"></label>
<label>Dostřel v metrech <input type="number" min="0" max="500" value="1"
                                name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_RANGE_IN_M ?>[0]"
                                required="required"></label>
<label>Útočnost <input type="number" min="=-20" max="50" value="0"
                       name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_OFFENSIVENESS ?>[0]"
                       required="required"></label>
<label>Zranění <input type="number" min="=-20" max="50" value="0"
                      name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_WOUNDS ?>[0]"
                      required="required"></label>
<label>Typ <select name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_WOUND_TYPE ?>[0]"
                   required="required">
        <?php foreach (WoundTypeCode::getPossibleValues() as $woundTypeValue) {
            $woundType = WoundTypeCode::getIt($woundTypeValue); ?>
            <option value="<?= $woundTypeValue ?>"><?= $woundType->translateTo('cs') ?></option>
        <?php } ?>
    </select>
</label>
<label>Kryt <input type="number" min="-10" max="20" value="0"
                   name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_COVER ?>[0]"
                   required="required"></label>
<label>Váha v kg <input type="number" min="0" max="99.99" value="1"
                        name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_WEIGHT ?>[0]"
                        required="required"></label>
<label>Pouze obouruční <input type="checkbox" value="1"
                              name="<?= CurrentValues::CUSTOM_RANGED_WEAPON_TWO_HANDED_ONLY ?>[0]"></label>
<input type="submit" value="Přidat">
<a class="button cancel" id="cancelNewRangedWeapon"
   href="<?= $controller->getCurrentUrlWithQuery([Controller::ACTION => '']); ?>">Zrušit</a>
