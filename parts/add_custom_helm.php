<?php
namespace DrdPlus\Calculators\Fight;

/** @var \DrdPlus\Calculators\Fight\Controller $controller */
?>
<label>Název <input type="text" name="<?= CurrentValues::CUSTOM_HELM_NAME ?>[0]"
                    required="required"></label>
<label>Potřebná síla <input type="number" min="-20" max="50" value="0"
                            name="<?= CurrentValues::CUSTOM_HELM_REQUIRED_STRENGTH ?>[0]"
                            required="required"></label>
<label>Omezení <input type="number" min="-10" max="20" value="0"
                      name="<?= CurrentValues::CUSTOM_HELM_RESTRICTION ?>[0]"
                      required="required"></label>
<label>Ochrana <input type="number" min="-10" max="20" value="1"
                      name="<?= CurrentValues::CUSTOM_HELM_PROTECTION ?>[0]"
                      required="required"></label>
<label>Váha v kg <input type="number" min="0" max="99.99" value="0.5"
                        name="<?= CurrentValues::CUSTOM_HELM_WEIGHT ?>[0]"
                        required="required"></label>
<input type="submit" value="Přidat helmu">
<a class="button cancel" id="cancelNewHelm"
   href="<?= $controller->getCurrentUrlWithQuery([Controller::ACTION => '']); ?>">Zrušit</a>
