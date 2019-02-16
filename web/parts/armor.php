<?php
namespace DrdPlus\FightCalculator;

/** @var \DrdPlus\AttackSkeleton\Web\BodyArmorBody $bodyArmorBody */
/** @var \DrdPlus\AttackSkeleton\Web\HelmBody $helmBody */
/** @var \DrdPlus\FightCalculator\Web\ArmorSkillBody $armorSkillBody */
?>
<div class="row">
  <h2 id="Zbroj" class="col"><a href="#Zbroj" class="inner">Zbroj a helma</a></h2>
</div>
<fieldset>
    <?php
    echo $bodyArmorBody;
    echo $helmBody;
    echo $armorSkillBody;
    ?>
  <div class="row"><input type="submit" value="Přepočítat"></div>
</fieldset>