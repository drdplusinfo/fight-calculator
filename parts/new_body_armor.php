<?php
namespace DrdPlus\Fight;

/** @var \DrdPlus\Fight\Controller $controller */
?>
<label>Název <input type="text" name="<?= CurrentValues::CUSTOM_BODY_ARMOR_NAME ?>[0]"
                    required="required"></label>
<label>Potřebná síla <input type="number" min="-20" max="50" value="0"
                            name="<?= CurrentValues::CUSTOM_BODY_ARMOR_REQUIRED_STRENGTH ?>[0]"
                            required="required"></label>
<label>Ochrana <input type="number" min="-10" max="20" value="0"
                      name="<?= CurrentValues::CUSTOM_BODY_ARMOR_PROTECTION ?>[0]"
                      required="required"></label>
<label>Váha v kg <input type="number" min="0" max="99.99" value="10"
                        name="<?= CurrentValues::CUSTOM_BODY_ARMOR_WEIGHT ?>[0]"
                        required="required"></label>
<label>Počet kol na obléknutí <input type="number" min="0" max="99" value="3"
                                     name="<?= CurrentValues::CUSTOM_BODY_ARMOR_ROUNDS_TO_PUT_ON ?>[0]"></label>
<input type="submit" value="Přidat">
<a class="button cancel" id="cancelNewBodyArmor"
   href="<?= $controller->getCurrentUrlWithQuery([Controller::ACTION => '']); ?>">Zrušit</a>
