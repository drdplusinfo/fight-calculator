<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\DistanceUnitCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

/** @var Controller $controller */
$selectedMeleeWeapon = $controller->getSelectedMeleeWeapon();
$selectedMeleeWeaponValue = $selectedMeleeWeapon ? $selectedMeleeWeapon->getValue() : null;
?>
<h4>Zbraň</h4>
<div class="panel">
    <label>
        <select name="<?= $controller::MELEE_WEAPON ?>" title="Melee weapon">
            <?php foreach ($controller->getMeleeWeaponCodes() as $weaponCategory => $meleeWeaponCategory) {
                ?>
                <optgroup label="<?= WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2) ?>">
                    <?php
                    /** @var string[] $meleeWeaponCategory */
                    foreach ($meleeWeaponCategory as $meleeWeaponValue) {
                        ?>
                        <option value="<?= $meleeWeaponValue ?>"
                                <?php if ($selectedMeleeWeaponValue === $meleeWeaponValue) { ?>selected<?php } ?>
                        >
                            <?= MeleeWeaponCode::getIt($meleeWeaponValue)->translateTo('cs') ?>
                        </option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
    </label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::MAIN_HAND ?>" name="<?= $controller::MELEE_HOLDING ?>"
               <?php if ($controller->getSelectedMeleeHolding()->getValue() === ItemHoldingCode::MAIN_HAND) { ?>checked<?php } ?>>
        v dominantní ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::OFFHAND ?>" name="<?= $controller::MELEE_HOLDING ?>"
               <?php if ($controller->getSelectedMeleeHolding()->getValue() === ItemHoldingCode::OFFHAND) { ?>checked<?php } ?>>
        v druhé
        ruce</label>
</div>
<div class="panel">
    <label>
        <input type="radio" value="<?= ItemHoldingCode::TWO_HANDS ?>" name="<?= $controller::MELEE_HOLDING ?>"
               <?php if ($controller->getSelectedMeleeHolding()->getValue() === ItemHoldingCode::TWO_HANDS) { ?>checked<?php } ?>>
        obouručně
    </label>
</div>
<div class="panel">
    <label>dovednost <select name="<?= $controller::MELEE_FIGHT_SKILL ?>">
            <?php
            $selectedSkillForMelee = $controller->getSelectedMeleeSkillCode();
            foreach ($controller->getPossibleSkillsForMelee() as $skillCode) {
                ?>
                <option value="<?= $skillCode->getValue() ?>"
                        <?php if ($selectedSkillForMelee->getValue() === $skillCode->getValue()) { ?>selected<?php } ?>>
                    <?= $skillCode->translateTo('cs') ?>
                </option>
            <?php } ?>
        </select>
    </label>
    <label>na stupni <input type="radio" value="0" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                              <?php if ($controller->getSelectedMeleeSkillRankValue() === 0) { ?>checked<?php } ?>> 0,
    </label>
    <label><input type="radio" value="1" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                    <?php if ($controller->getSelectedMeleeSkillRankValue() === 1) { ?>checked<?php } ?>> 1,
    </label>
    <label><input type="radio" value="2" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                    <?php if ($controller->getSelectedMeleeSkillRankValue() === 2) { ?>checked<?php } ?>> 2,
    </label>
    <label><input type="radio" value="3" name="<?= $controller::MELEE_FIGHT_SKILL_RANK ?>"
                    <?php if ($controller->getSelectedMeleeSkillRankValue() === 3) { ?>checked<?php } ?>> 3
    </label>
</div>
<div><input type="submit" value="OK"></div>
<div class="block">
    <div>
        <?php $meleeFightProperties = $controller->getMeleeWeaponFightProperties(); ?>
        <div>Bojové číslo <span class="hint">se zbraní</span>: <?= $meleeFightProperties->getFightNumber() ?>
        </div>
        <div>
            ÚČ <span class="hint">se zbraní</span>: <?= $meleeFightProperties->getAttackNumber(
                new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
                Size::getIt(0)
            ) ?>
        </div>
        <div>
            ZZ <span class="hint">se zbraní</span>: <?= $meleeFightProperties->getBaseOfWounds() ?>
        </div>
        <div>
            Obranné číslo <span
                    class="hint">se zbraní</span>: <?= $meleeFightProperties->getDefenseNumberWithShield() ?>
        </div>
    </div>
</div>
