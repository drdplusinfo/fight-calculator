<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web;

use DrdPlus\AttackSkeleton\AttackServicesContainer;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomBodyArmorBody;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomHelmBody;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomMeleeWeaponBody;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomRangedWeaponBody;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomShieldBody;
use DrdPlus\CalculatorSkeleton\Web\CalculatorWebPartsContainer;

class AttackWebPartsContainer extends CalculatorWebPartsContainer
{
    /** @var BodyPropertiesBody */
    private $bodyPropertiesBody;
    /** @var BodyArmorBody */
    private $bodyArmorBody;
    /** @var AddCustomBodyArmorBody */
    private $addCustomBodyArmorBody;
    /** @var HelmBody */
    private $helmBody;
    /** @var AddCustomHelmBody */
    private $addCustomHelmBody;
    /** @var MeleeWeaponBody */
    private $meleeWeaponBody;
    /** @var AddCustomMeleeWeaponBody */
    private $addCustomMeleeWeaponBody;
    /** @var RangedWeaponBody */
    private $rangedWeaponBody;
    /** @var AddCustomRangedWeaponBody */
    private $addCustomRangedWeaponBody;
    /** @var ShieldBody */
    private $shieldBody;
    /** @var AddCustomShieldBody */
    private $addCustomShieldBody;
    /** @var AttackServicesContainer */
    private $attackServicesContainer;

    public function __construct(AttackServicesContainer $attackServicesContainer)
    {
        parent::__construct($attackServicesContainer);
        $this->attackServicesContainer = $attackServicesContainer;
    }

    public function getBodyPropertiesBody(): BodyPropertiesBody
    {
        if ($this->bodyPropertiesBody === null) {
            $this->bodyPropertiesBody = new BodyPropertiesBody($this->attackServicesContainer->getCurrentProperties());
        }

        return $this->bodyPropertiesBody;
    }

    public function getBodyArmorBody(): BodyArmorBody
    {
        if ($this->bodyArmorBody === null) {
            $this->bodyArmorBody = new BodyArmorBody(
                $this->attackServicesContainer->getCustomArmamentsState(),
                $this->attackServicesContainer->getCurrentArmamentsValues(),
                $this->attackServicesContainer->getCurrentArmaments(),
                $this->attackServicesContainer->getPossibleArmaments(),
                $this->attackServicesContainer->getArmamentsUsabilityMessages(),
                $this->attackServicesContainer->getHtmlHelper(),
                $this->attackServicesContainer->getArmourer(),
                $this->getAddCustomBodyArmorBody()
            );
        }

        return $this->bodyArmorBody;
    }

    public function getAddCustomBodyArmorBody(): AddCustomBodyArmorBody
    {
        if ($this->addCustomBodyArmorBody === null) {
            $this->addCustomBodyArmorBody = new AddCustomBodyArmorBody($this->attackServicesContainer->getHtmlHelper());
        }

        return $this->addCustomBodyArmorBody;
    }

    public function getHelmBody(): HelmBody
    {
        if ($this->helmBody === null) {
            $this->helmBody = new HelmBody(
                $this->attackServicesContainer->getCustomArmamentsState(),
                $this->attackServicesContainer->getCurrentArmamentsValues(),
                $this->attackServicesContainer->getCurrentArmaments(),
                $this->attackServicesContainer->getPossibleArmaments(),
                $this->attackServicesContainer->getArmamentsUsabilityMessages(),
                $this->attackServicesContainer->getHtmlHelper(),
                $this->attackServicesContainer->getArmourer(),
                $this->getAddCustomHelmBody()
            );
        }

        return $this->helmBody;
    }

    public function getAddCustomHelmBody(): AddCustomHelmBody
    {
        if ($this->addCustomHelmBody === null) {
            $this->addCustomHelmBody = new AddCustomHelmBody($this->attackServicesContainer->getHtmlHelper());
        }

        return $this->addCustomHelmBody;
    }

    public function getMeleeWeaponBody(): MeleeWeaponBody
    {
        if ($this->meleeWeaponBody === null) {
            $this->meleeWeaponBody = new MeleeWeaponBody(
                $this->attackServicesContainer->getCustomArmamentsState(),
                $this->attackServicesContainer->getCurrentArmamentsValues(),
                $this->attackServicesContainer->getCurrentArmaments(),
                $this->attackServicesContainer->getPossibleArmaments(),
                $this->attackServicesContainer->getArmamentsUsabilityMessages(),
                $this->attackServicesContainer->getHtmlHelper(),
                $this->getAddCustomMeleeWeaponBody()
            );
        }

        return $this->meleeWeaponBody;
    }

    public function getAddCustomMeleeWeaponBody(): AddCustomMeleeWeaponBody
    {
        if ($this->addCustomMeleeWeaponBody === null) {
            $this->addCustomMeleeWeaponBody = new AddCustomMeleeWeaponBody($this->attackServicesContainer->getHtmlHelper());
        }

        return $this->addCustomMeleeWeaponBody;
    }

    public function getRangedWeaponBody(): RangedWeaponBody
    {
        if ($this->rangedWeaponBody === null) {
            $this->rangedWeaponBody = new RangedWeaponBody(
                $this->attackServicesContainer->getCustomArmamentsState(),
                $this->attackServicesContainer->getCurrentArmamentsValues(),
                $this->attackServicesContainer->getCurrentArmaments(),
                $this->attackServicesContainer->getPossibleArmaments(),
                $this->attackServicesContainer->getArmamentsUsabilityMessages(),
                $this->attackServicesContainer->getHtmlHelper(),
                $this->getAddCustomRangedWeaponBody()
            );
        }

        return $this->rangedWeaponBody;
    }

    public function getAddCustomRangedWeaponBody(): AddCustomRangedWeaponBody
    {
        if ($this->addCustomRangedWeaponBody === null) {
            $this->addCustomRangedWeaponBody = new AddCustomRangedWeaponBody($this->attackServicesContainer->getHtmlHelper());
        }

        return $this->addCustomRangedWeaponBody;
    }

    public function getShieldBody(): ShieldBody
    {
        if ($this->shieldBody === null) {
            $this->shieldBody = new ShieldBody(
                $this->attackServicesContainer->getCustomArmamentsState(),
                $this->attackServicesContainer->getCurrentArmamentsValues(),
                $this->attackServicesContainer->getCurrentArmaments(),
                $this->attackServicesContainer->getPossibleArmaments(),
                $this->attackServicesContainer->getArmamentsUsabilityMessages(),
                $this->attackServicesContainer->getHtmlHelper(),
                $this->attackServicesContainer->getArmourer(),
                $this->getAddCustomShieldBody()
            );
        }

        return $this->shieldBody;
    }

    public function getAddCustomShieldBody(): AddCustomShieldBody
    {
        if ($this->addCustomShieldBody === null) {
            $this->addCustomShieldBody = new AddCustomShieldBody($this->attackServicesContainer->getHtmlHelper());
        }

        return $this->addCustomShieldBody;
    }

}