<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CurrentProperties;
use DrdPlus\AttackSkeleton\CustomArmamentsState;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomBodyArmorBody;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomHelmBody;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomMeleeWeaponBody;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomRangedWeaponBody;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomShieldBody;
use DrdPlus\CalculatorSkeleton\Web\CalculatorWebPartsContainer;
use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\UsagePolicy;
use DrdPlus\RulesSkeleton\Web\Tools\WebFiles;

class AttackWebPartsContainer extends CalculatorWebPartsContainer
{
    private ?\DrdPlus\AttackSkeleton\Web\BodyPropertiesBody $bodyPropertiesBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\BodyArmorBody $bodyArmorBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomBodyArmorBody $addCustomBodyArmorBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\HelmBody $helmBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomHelmBody $addCustomHelmBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\MeleeWeaponBody $meleeWeaponBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomMeleeWeaponBody $addCustomMeleeWeaponBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\RangedWeaponBody $rangedWeaponBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomRangedWeaponBody $addCustomRangedWeaponBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\ShieldBody $shieldBody = null;
    private ?\DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomShieldBody $addCustomShieldBody = null;
    private \DrdPlus\AttackSkeleton\CurrentProperties $currentProperties;
    /** @var HtmlHelper */
    private $htmlHelper;
    private \DrdPlus\AttackSkeleton\CustomArmamentsState $customArmamentsState;
    private \DrdPlus\AttackSkeleton\CurrentArmamentsValues $currentArmamentsValues;
    private \DrdPlus\AttackSkeleton\CurrentArmaments $currentArmaments;
    private \DrdPlus\AttackSkeleton\PossibleArmaments $possibleArmaments;
    private \DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages $armamentsUsabilityMessages;
    private \DrdPlus\Armourer\Armourer $armourer;

    public function __construct(
        Configuration $configuration,
        UsagePolicy $usagePolicy,
        WebFiles $webFiles,
        Dirs $dirs,
        HtmlHelper $htmlHelper,
        Request $request,
        CurrentProperties $currentProperties,
        CustomArmamentsState $customArmamentsState,
        CurrentArmamentsValues $currentArmamentsValues,
        CurrentArmaments $currentArmaments,
        PossibleArmaments $possibleArmaments,
        ArmamentsUsabilityMessages $armamentsUsabilityMessages,
        Armourer $armourer
    )
    {
        parent::__construct($configuration, $usagePolicy, $webFiles, $dirs, $htmlHelper, $request);
        $this->currentProperties = $currentProperties;
        $this->htmlHelper = $htmlHelper;
        $this->customArmamentsState = $customArmamentsState;
        $this->currentArmamentsValues = $currentArmamentsValues;
        $this->currentArmaments = $currentArmaments;
        $this->possibleArmaments = $possibleArmaments;
        $this->armamentsUsabilityMessages = $armamentsUsabilityMessages;
        $this->armourer = $armourer;
    }

    public function getBodyPropertiesBody(): BodyPropertiesBody
    {
        if ($this->bodyPropertiesBody === null) {
            $this->bodyPropertiesBody = new BodyPropertiesBody($this->currentProperties);
        }
        return $this->bodyPropertiesBody;
    }

    public function getBodyArmorBody(): BodyArmorBody
    {
        if ($this->bodyArmorBody === null) {
            $this->bodyArmorBody = new BodyArmorBody(
                $this->customArmamentsState,
                $this->currentArmamentsValues,
                $this->currentArmaments,
                $this->possibleArmaments,
                $this->armamentsUsabilityMessages,
                $this->htmlHelper,
                $this->armourer,
                $this->getAddCustomBodyArmorBody()
            );
        }
        return $this->bodyArmorBody;
    }

    public function getAddCustomBodyArmorBody(): AddCustomBodyArmorBody
    {
        if ($this->addCustomBodyArmorBody === null) {
            $this->addCustomBodyArmorBody = new AddCustomBodyArmorBody($this->htmlHelper);
        }
        return $this->addCustomBodyArmorBody;
    }

    public function getHelmBody(): HelmBody
    {
        if ($this->helmBody === null) {
            $this->helmBody = new HelmBody(
                $this->customArmamentsState,
                $this->currentArmamentsValues,
                $this->currentArmaments,
                $this->possibleArmaments,
                $this->armamentsUsabilityMessages,
                $this->htmlHelper,
                $this->armourer,
                $this->getAddCustomHelmBody()
            );
        }
        return $this->helmBody;
    }

    public function getAddCustomHelmBody(): AddCustomHelmBody
    {
        if ($this->addCustomHelmBody === null) {
            $this->addCustomHelmBody = new AddCustomHelmBody($this->htmlHelper);
        }
        return $this->addCustomHelmBody;
    }

    public function getMeleeWeaponBody(): MeleeWeaponBody
    {
        if ($this->meleeWeaponBody === null) {
            $this->meleeWeaponBody = new MeleeWeaponBody(
                $this->customArmamentsState,
                $this->currentArmamentsValues,
                $this->currentArmaments,
                $this->possibleArmaments,
                $this->armamentsUsabilityMessages,
                $this->htmlHelper,
                $this->getAddCustomMeleeWeaponBody()
            );
        }
        return $this->meleeWeaponBody;
    }

    public function getAddCustomMeleeWeaponBody(): AddCustomMeleeWeaponBody
    {
        if ($this->addCustomMeleeWeaponBody === null) {
            $this->addCustomMeleeWeaponBody = new AddCustomMeleeWeaponBody($this->htmlHelper);
        }
        return $this->addCustomMeleeWeaponBody;
    }

    public function getRangedWeaponBody(): RangedWeaponBody
    {
        if ($this->rangedWeaponBody === null) {
            $this->rangedWeaponBody = new RangedWeaponBody(
                $this->customArmamentsState,
                $this->currentArmamentsValues,
                $this->currentArmaments,
                $this->possibleArmaments,
                $this->armamentsUsabilityMessages,
                $this->htmlHelper,
                $this->getAddCustomRangedWeaponBody()
            );
        }
        return $this->rangedWeaponBody;
    }

    public function getAddCustomRangedWeaponBody(): AddCustomRangedWeaponBody
    {
        if ($this->addCustomRangedWeaponBody === null) {
            $this->addCustomRangedWeaponBody = new AddCustomRangedWeaponBody($this->htmlHelper);
        }
        return $this->addCustomRangedWeaponBody;
    }

    public function getShieldBody(): ShieldBody
    {
        if ($this->shieldBody === null) {
            $this->shieldBody = new ShieldBody(
                $this->customArmamentsState,
                $this->currentArmamentsValues,
                $this->currentArmaments,
                $this->possibleArmaments,
                $this->armamentsUsabilityMessages,
                $this->htmlHelper,
                $this->armourer,
                $this->getAddCustomShieldBody()
            );
        }
        return $this->shieldBody;
    }

    public function getAddCustomShieldBody(): AddCustomShieldBody
    {
        if ($this->addCustomShieldBody === null) {
            $this->addCustomShieldBody = new AddCustomShieldBody($this->htmlHelper);
        }
        return $this->addCustomShieldBody;
    }

}
