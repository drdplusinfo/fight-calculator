<?php
namespace DrdPlus\FightCalculator;

/** @var FightController $controller */
?>
<div class="row">
  <h2 id="Zbroj" class="col"><a href="#Zbroj" class="inner">Zbroj a helma</a></h2>
</div>
<fieldset>
    <?php
    echo $controller->getArmorContent();
    echo $controller->getHelmContent();
    ?>
  <div class="row skill">
    <div class="col">
      <label>
            <span class="keyword"><a target="_blank" href="https://pph.drdplus.info/#noseni_zbroje">
                    <?= $controller->getFight()->getSkillForArmor()->translateTo('cs') ?></a>
            </span>
      </label>
    </div>
    <div class="col">
      <label>na stupni <input type="radio" value="0" name="<?= FightController::ARMOR_SKILL_VALUE ?>"
                              <?php if ($controller->getFight()->getCurrentArmorSkillRank() === 0) { ?>checked<?php } ?>>
        0,
      </label>
      <label><input type="radio" value="1" name="<?= FightController::ARMOR_SKILL_VALUE ?>"
                    <?php if ($controller->getFight()->getCurrentArmorSkillRank() === 1) { ?>checked<?php } ?>> 1,
      </label>
      <label><input type="radio" value="2" name="<?= FightController::ARMOR_SKILL_VALUE ?>"
                    <?php if ($controller->getFight()->getCurrentArmorSkillRank() === 2) { ?>checked<?php } ?>> 2,
      </label>
      <label><input type="radio" value="3" name="<?= FightController::ARMOR_SKILL_VALUE ?>"
                    <?php if ($controller->getFight()->getCurrentArmorSkillRank() === 3) { ?>checked<?php } ?>> 3
      </label>
    </div>
  </div>
  <div class="row"><input type="submit" value="Přepočítat"></div>
</fieldset>