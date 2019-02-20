<form action="" method="get">
    <?php
    /** @var \DrdPlus\AttackSkeleton\Web\AttackWebPartsContainer $webPartsContainer */
    echo $webPartsContainer->getBodyPropertiesBody()->getValue();
    echo $webPartsContainer->getBodyArmorBody()->getValue();
    echo $webPartsContainer->getHelmBody()->getValue();
    echo $webPartsContainer->getMeleeWeaponBody()->getValue();
    echo $webPartsContainer->getRangedWeaponBody()->getValue();
    echo $webPartsContainer->getShieldBody()->getValue();
    ?>
  <button type="submit">Odeslat</button>
</form>
