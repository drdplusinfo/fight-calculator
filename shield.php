<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\DistanceUnitCode;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

/** @var Controller $controller */
?>

<h4>Štít</h4>
<div class="panel">
    <label>
        <select
                name="<?= $controller::SHIELD ?>"><?php foreach ($controller->getPossibleShields() as $shield) { ?>
                <option value="<?= $shield->getValue() ?>"
                        <?php if ($controller->getSelectedShield()->getValue() === $shield->getValue()){ ?>selected<?php } ?>>
                    <?= $shield->translateTo('cs') ?>
                </option>
            <?php } ?>
        </select>
    </label>
</div>
<div class="panel">
    <label>
        dovednost <span class="keyword"><?= $controller->getShieldUsageSkillCode()->translateTo('cs') ?></span>
        na stupni
        <input
                type="number" min="0" max="3"
                name="<?= $controller::SHIELD_USAGE_SKILL_RANK ?>"
                value="<?= $controller->getSelectedShieldSkillRank() ?>">
    </label>
</div>
<div class="panel">
    <label>
        dovednost <span
                class="keyword"><?= $controller->getFightWithShieldsSkillCode()->translateTo('cs') ?></span>
        na stupni
        <input
                type="number" min="0" max="3"
                name="<?= $controller::FIGHT_WITH_SHIELDS_SKILL_RANK ?>"
                value="<?= $controller->getSelectedFightWithShieldsSkillRank() ?>">
    </label>
</div>
<div><input type="submit" value="OK"></div>
<div class="panel">
    <?php $rangedFightProperties = $controller->getShieldFightProperties(); ?>
    <div>Bojové číslo <span class="hint">se štítem jako zbraň</span>: <?= $rangedFightProperties->getFightNumber() ?>
    </div>
    <div>
        ÚČ <span class="hint">se štítem jako zbraň</span>: <?= $rangedFightProperties->getAttackNumber(
            new Distance(1, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable()),
            Size::getIt(0)
        ) ?>
    </div>
    <div>
        ZZ <span class="hint">se štítem jako zbraň</span>: <?= $rangedFightProperties->getBaseOfWounds() ?>
    </div>
    <div>Obranné číslo <span class="hint">se štítem</span>: <?= $rangedFightProperties->getDefenseNumberWithShield() ?>
    </div>
</div>
