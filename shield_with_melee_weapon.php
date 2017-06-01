<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\DistanceUnitCode;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

/** @var Controller $controller */
?>

<h4>Štít ke zbrani na blízko</h4>
<div class="panel">
    <label>
        <select
                name="<?= $controller::MELEE_SHIELD ?>"><?php foreach ($controller->getPossibleShields() as $shield) { ?>
                <option value="<?= $shield->getValue() ?>"
                        <?php if ($controller->getSelectedMeleeShield()->getValue() === $shield->getValue()){ ?>selected<?php } ?>>
                    <?= $shield->translateTo('cs') ?>
                </option>
            <?php } ?>
        </select>
    </label>
</div>
<div class="panel">
    dovednost <span class="keyword"><?= $controller->getShieldUsageSkillCode()->translateTo('cs') ?></span>
    <label>na stupni <input type="radio" value="0" name="<?= $controller::MELEE_SHIELD_USAGE_SKILL_RANK ?>"
                            <?php if ($controller->getSelectedMeleeShieldUsageSkillRank() === 0) { ?>checked<?php } ?>> 0,
    </label>
    <label><input type="radio" value="1" name="<?= $controller::MELEE_SHIELD_USAGE_SKILL_RANK ?>"
                  <?php if ($controller->getSelectedMeleeShieldUsageSkillRank() === 1) { ?>checked<?php } ?>> 1,
    </label>
    <label><input type="radio" value="2" name="<?= $controller::MELEE_SHIELD_USAGE_SKILL_RANK ?>"
                  <?php if ($controller->getSelectedMeleeShieldUsageSkillRank() === 2) { ?>checked<?php } ?>> 2,
    </label>
    <label><input type="radio" value="3" name="<?= $controller::MELEE_SHIELD_USAGE_SKILL_RANK ?>"
                  <?php if ($controller->getSelectedMeleeShieldUsageSkillRank() === 3) { ?>checked<?php } ?>> 3
    </label>
</div>
<div class="panel">
    <label>
        dovednost <span
                class="keyword"><?= $controller->getFightWithShieldsSkillCode()->translateTo('cs') ?></span>
    </label>
    <label>na stupni <input type="radio" value="0" name="<?= $controller::MELEE_FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                            <?php if ($controller->getSelectedMeleeFightWithShieldsSkillRank() === 0) { ?>checked<?php } ?>>
        0,
    </label>
    <label><input type="radio" value="1" name="<?= $controller::MELEE_FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                  <?php if ($controller->getSelectedMeleeFightWithShieldsSkillRank() === 1) { ?>checked<?php } ?>> 1,
    </label>
    <label><input type="radio" value="2" name="<?= $controller::MELEE_FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                  <?php if ($controller->getSelectedMeleeFightWithShieldsSkillRank() === 2) { ?>checked<?php } ?>> 2,
    </label>
    <label><input type="radio" value="3" name="<?= $controller::MELEE_FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                  <?php if ($controller->getSelectedMeleeFightWithShieldsSkillRank() === 3) { ?>checked<?php } ?>> 3
    </label>
</div>
<div class="block"><input type="submit" value="OK"></div>
<div class="block">
    <?php $meleeShieldFightProperties = $controller->getMeleeShieldFightProperties(); ?>
    <div>Bojové číslo <span class="hint">se štítem jako zbraň</span>: <?= $meleeShieldFightProperties->getFightNumber() ?>
    </div>
    <div>
        ÚČ <span class="hint">se štítem jako zbraň</span>: <?= $meleeShieldFightProperties->getAttackNumber(
            new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
            Size::getIt(0)
        ) ?>
    </div>
    <div>
        ZZ <span class="hint">se štítem jako zbraň</span>: <?= $meleeShieldFightProperties->getBaseOfWounds() ?>
    </div>
    <div>Obranné číslo <span class="hint">se štítem</span>: <?= $meleeShieldFightProperties->getDefenseNumberWithShield() ?>
    </div>
    <div>držen <?= $controller->getMeleeShieldHolding()->translateTo('cs') ?></div>
</div>
