<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages;
use DrdPlus\AttackSkeleton\AttackRequest;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CustomArmamentsState;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomShieldBody;
use DrdPlus\Codes\Armaments\ShieldCode;

class ShieldBody extends AbstractArmamentBody
{
    /** @var AddCustomShieldBody */
    private $addCustomShieldBody;
    /** @var CustomArmamentsState */
    private $customArmamentsState;
    /** @var CurrentArmamentsValues */
    private $currentArmamentsValues;
    /** @var CurrentArmaments */
    private $currentArmaments;
    /** @var ArmamentsUsabilityMessages */
    private $armamentsUsabilityMessages;
    /** @var HtmlHelper */
    private $frontendHelper;
    /** @var PossibleArmaments */
    private $possibleArmaments;
    /** @var Armourer */
    private $armourer;

    public function __construct(
        CustomArmamentsState $customArmamentsState,
        CurrentArmamentsValues $currentArmamentsValues,
        CurrentArmaments $currentArmaments,
        PossibleArmaments $possibleArmaments,
        ArmamentsUsabilityMessages $armamentsUsabilityMessages,
        HtmlHelper $frontendHelper,
        Armourer $armourer,
        AddCustomShieldBody $addCustomShieldBody
    )
    {
        $this->customArmamentsState = $customArmamentsState;
        $this->currentArmamentsValues = $currentArmamentsValues;
        $this->currentArmaments = $currentArmaments;
        $this->armamentsUsabilityMessages = $armamentsUsabilityMessages;
        $this->frontendHelper = $frontendHelper;
        $this->possibleArmaments = $possibleArmaments;
        $this->armourer = $armourer;
        $this->addCustomShieldBody = $addCustomShieldBody;
    }

    public function getValue(): string
    {
        return <<<HTML
{$this->getAddShield()}
{$this->getCurrentCustomShields()}
<div class="row {$this->getVisibilityClass()}" id="chooseShield">
  <div class="col">
    <div class="messages">
        {$this->getMessagesAboutShields()}
    </div>
    <a title="Přidat vlastní štít" href="{$this->getLinkToAddNewShield()}" class="button add">+</a>
    <label>
      <select name="{$this->getShieldSelectName()}" title="Štít">
         {$this->getPossibleShields()} 
      </select>
    </label>
  </div>
</div>
HTML;
    }

    private function getPossibleShields(): string
    {
        $shields = [];
        foreach ($this->possibleArmaments->getPossibleShields() as $possibleShield) {
            /** @var ShieldCode $shieldCode */
            $shieldCode = $possibleShield['code'];
            $shields[] = <<<HTML
<option value="{$shieldCode->getValue()}" {$this->getShieldSelected($shieldCode)} {$this->getDisabled($possibleShield['canUseIt'])}>
  {$this->getUsabilityPictogram($possibleShield['canUseIt'])}{$shieldCode->translateTo('cs')} {$this->getShieldProtection($shieldCode)}
</option>
HTML;
        }

        return \implode("\n", $shields);
    }

    private function getShieldProtection(ShieldCode $shieldCode): string
    {
        return $this->frontendHelper->formatInteger($this->armourer->getCoverOfShield($shieldCode));
    }

    private function getShieldSelected(ShieldCode $shieldCode): string
    {
        return $this->getSelected($this->currentArmaments->getCurrentShield(), $shieldCode);
    }

    private function getShieldSelectName(): string
    {
        return AttackRequest::SHIELD;
    }

    private function getLinkToAddNewShield(): string
    {
        return $this->frontendHelper->getLocalUrlToAction(AttackRequest::ADD_NEW_SHIELD);
    }

    private function getVisibilityClass(): string
    {
        return $this->customArmamentsState->isAddingNewShield()
            ? 'hidden'
            : '';
    }

    private function getMessagesAboutShields(): string
    {
        $messagesAboutShields = '';
        foreach ($this->armamentsUsabilityMessages->getMessagesAboutShields() as $messageAboutShield) {
            $messagesAboutShields .= <<<HTML
          <div class="info">$messageAboutShield</div>
HTML;
        }

        return $messagesAboutShields;
    }

    private function getAddShield(): string
    {
        if (!$this->customArmamentsState->isAddingNewShield()) {
            return '';
        }

        return <<<HTML
<div id="addShield" class="row add">
  {$this->addCustomShieldBody->getValue()}
</div>
HTML;
    }

    private function getCurrentCustomShields(): string
    {
        $possibleCustomShields = '';
        foreach ($this->currentArmamentsValues->getCurrentCustomShieldsValues() as $armorName => $armorValues) {
            /** @var array|string[] $armorValues */
            foreach ($armorValues as $typeName => $armorValue) {
                $possibleCustomShields .= <<<HTML
<input type="hidden" name="{$typeName}[{$armorName}]" value="<{$armorValue}">
HTML;
            }
        }

        return $possibleCustomShields;
    }
}