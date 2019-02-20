<?php /** @var \DrdPlus\FightCalculator\Web\FightWebPartsContainer $webPartsContainer */ ?>
<div class="row">
  <h2 id="Zbroj" class="col"><a href="#Zbroj" class="inner">Zbroj a helma</a></h2>
</div>
<fieldset>
    <?php
    echo $webPartsContainer->getBodyArmorBody();
    echo $webPartsContainer->getHelmBody();
    echo $webPartsContainer->getArmorSkillBody();
    ?>
  <div class="row"><input type="submit" value="Přepočítat"></div>
</fieldset>