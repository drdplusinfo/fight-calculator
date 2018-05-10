<?php
namespace DrdPlus\Calculator\Fight;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Codes\Skills\PsychicalSkillCode;

/** @var Controller $controller */
?>
<div class="panel">
    <div class="block">
        <label>
            <input type="checkbox" value="1"
                   name="<?= Controller::ON_HORSEBACK ?>"
                   <?php if ($controller->getFight()->getCurrentOnHorseback()) { ?>checked="checked" <?php } ?>>
            Bojuješ ze sedla
        </label>
    </div>
    <div class="block">
        Dovednost <span class="keyword">
            <a href="https://pph.drdplus.info/#jezdectvi" target="_blank">
                <?= PhysicalSkillCode::getIt(PhysicalSkillCode::RIDING)->translateTo('cs') ?></a>
        </span>
        <label>na stupni <input type="radio" value="0" name="<?= Controller::RIDING_SKILL_RANK ?>"
                                <?php if ($controller->getFight()->getSelectedRidingSkillRank() === 0) { ?>checked<?php } ?>>
            0,
        </label>
        <label><input type="radio" value="1" name="<?= Controller::RIDING_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedRidingSkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= Controller::RIDING_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedRidingSkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= Controller::RIDING_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedRidingSkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
</div>
<div class="panel">
    <div class="block">
        <label><input type="checkbox" value="1"
                      name="<?= Controller::FIGHT_FREE_WILL_ANIMAL ?>"
                      <?php if ($controller->getFight()->getSelectedFightFreeWillAnimal()) { ?>checked="checked" <?php } ?>>
            Bojuješ se zvířetem s vlastní vůlí</label>
    </div>
    <div class="block">
        Dovednost <span class="keyword">
            <a href="https://pph.drdplus.info/#zoologie" target="_blank">
                <?= PsychicalSkillCode::getIt(PsychicalSkillCode::ZOOLOGY)->translateTo('cs') ?></a>
        </span>
        <label>na stupni <input type="radio" value="0" name="<?= Controller::ZOOLOGY_SKILL_RANK ?>"
                                <?php if ($controller->getFight()->getSelectedZoologySkillRank() === 0) { ?>checked<?php } ?>>
            0,
        </label>
        <label><input type="radio" value="1" name="<?= Controller::ZOOLOGY_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedZoologySkillRank() === 1) { ?>checked<?php } ?>> 1,
        </label>
        <label><input type="radio" value="2" name="<?= Controller::ZOOLOGY_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedZoologySkillRank() === 2) { ?>checked<?php } ?>> 2,
        </label>
        <label><input type="radio" value="3" name="<?= Controller::ZOOLOGY_SKILL_RANK ?>"
                      <?php if ($controller->getFight()->getSelectedZoologySkillRank() === 3) { ?>checked<?php } ?>> 3
        </label>
    </div>
</div>
<div class="block"><input type="submit" value="Přepočítat"></div>
