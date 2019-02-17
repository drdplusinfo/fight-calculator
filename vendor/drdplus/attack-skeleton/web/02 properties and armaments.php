<form action="" method="get">
    <?php
    /** @var \DrdPlus\AttackSkeleton\Web\BodyPropertiesBody $bodyPropertiesBody */
    echo $bodyPropertiesBody->getValue();
    /** @var \DrdPlus\AttackSkeleton\Web\BodyArmorBody $bodyArmorBody */
    echo $bodyArmorBody->getValue();
    /** @var \DrdPlus\AttackSkeleton\Web\HelmBody $helmBody */
    echo $helmBody->getValue();
    /** @var \DrdPlus\AttackSkeleton\Web\MeleeWeaponBody $meleeWeaponBody */
    echo $meleeWeaponBody->getValue();
    /** @var \DrdPlus\AttackSkeleton\Web\RangedWeaponBody $rangedWeaponBody */
    echo $rangedWeaponBody->getValue();
    /** @var \DrdPlus\AttackSkeleton\Web\ShieldBody $shieldBody */
    echo $shieldBody->getValue();
    ?>
  <button type="submit">Odeslat</button>
</form>
