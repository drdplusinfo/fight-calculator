<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages;
use DrdPlus\AttackSkeleton\AttackRequest;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CustomArmamentsState;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomHelmBody;
use DrdPlus\Codes\Armaments\HelmCode;

class HelmBody extends AbstractArmamentBody
{
    /** @var CustomArmamentsState */
    private $customArmamentsState;
    /** @var CurrentArmamentsValues */
    private $currentArmamentsValues;
    /** @var CurrentArmaments */
    private $currentArmaments;
    /** @var ArmamentsUsabilityMessages */
    private $armamentsUsabilityMessages;
    /** @var HtmlHelper */
    private $htmlHelper;
    /** @var PossibleArmaments */
    private $possibleArmaments;
    /** @var Armourer */
    private $armourer;
    /** @var AddCustomHelmBody */
    private $addCustomHelmBody;

    public function __construct(
        CustomArmamentsState $customArmamentsState,
        CurrentArmamentsValues $currentArmamentsValues,
        CurrentArmaments $currentArmaments,
        PossibleArmaments $possibleArmaments,
        ArmamentsUsabilityMessages $armamentsUsabilityMessages,
        HtmlHelper $htmlHelper,
        Armourer $armourer,
        AddCustomHelmBody $addCustomHelmBody
    )
    {
        $this->customArmamentsState = $customArmamentsState;
        $this->currentArmamentsValues = $currentArmamentsValues;
        $this->currentArmaments = $currentArmaments;
        $this->armamentsUsabilityMessages = $armamentsUsabilityMessages;
        $this->htmlHelper = $htmlHelper;
        $this->possibleArmaments = $possibleArmaments;
        $this->armourer = $armourer;
        $this->addCustomHelmBody = $addCustomHelmBody;
    }

    public function getValue(): string
    {
        return <<<HTML
{$this->getAddHelm()}
{$this->getCurrentCustomHelms()}
<div class="row {$this->getVisibilityClass()}" id="chooseHelm">
  <div class="col">
    <div class="messages">
        {$this->getMessagesAboutHelms()}
    </div>
    <a title="Přidat vlastní helmu" href="{$this->getLinkToAddNewHelm()}" class="btn btn-success btn-sm add">+</a>
    <label>
      <select name="{$this->getHelmSelectName()}" title="Helma">
         {$this->getPossibleHelms()} 
      </select>
    </label>
  </div>
</div>
HTML;
    }

    private function getPossibleHelms(): string
    {
        $helms = [];
        foreach ($this->possibleArmaments->getPossibleHelms() as $possibleHelm) {
            /** @var HelmCode $helmCode */
            $helmCode = $possibleHelm['code'];
            $helms[] = <<<HTML
<option value="{$helmCode->getValue()}" {$this->getHelmSelected($helmCode)} {$this->htmlHelper->getDisabled($possibleHelm['canUseIt'])}>
  {$this->getUsabilityPictogram($possibleHelm['canUseIt'])}{$helmCode->translateTo('cs')} {$this->getHelmProtection($helmCode)}
</option>
HTML;
        }

        return \implode("\n", $helms);
    }

    private function getHelmProtection(HelmCode $helmCode): string
    {
        return $this->htmlHelper->formatInteger($this->armourer->getProtectionOfHelm($helmCode));
    }

    private function getHelmSelected(HelmCode $helmCode): string
    {
        return $this->htmlHelper->getSelected($this->currentArmaments->getCurrentHelm(), $helmCode);
    }

    private function getHelmSelectName(): string
    {
        return AttackRequest::HELM;
    }

    private function getLinkToAddNewHelm(): string
    {
        return $this->htmlHelper->getLocalUrlToAction(AttackRequest::ADD_NEW_HELM);
    }

    private function getVisibilityClass(): string
    {
        return $this->customArmamentsState->isAddingNewHelm()
            ? 'hidden'
            : '';
    }

    private function getMessagesAboutHelms(): string
    {
        $messagesAboutHelms = [];
        foreach ($this->armamentsUsabilityMessages->getMessagesAboutHelms() as $messageAboutHelm) {
            $messagesAboutHelms [] = <<<HTML
          <div class="alert alert-primary">{$messageAboutHelm}</div>
HTML;
        }

        return \implode("\n", $messagesAboutHelms);
    }

    private function getAddHelm(): string
    {
        if (!$this->customArmamentsState->isAddingNewHelm()) {
            return '';
        }

        return <<<HTML
<div id="addHelm" class="row add">
  {$this->addCustomHelmBody->getValue()}
</div>
HTML;
    }

    private function getCurrentCustomHelms(): string
    {
        $possibleCustomHelms = [];
        foreach ($this->currentArmamentsValues->getCurrentCustomHelmsValues() as $armorName => $armorValues) {
            /** @var array|string[] $armorValues */
            foreach ($armorValues as $typeName => $armorValue) {
                $possibleCustomHelms [] = <<<HTML
<input type="hidden" name="{$typeName}[{$armorName}]" value="{$armorValue}">
HTML;
            }
        }

        return \implode("\n", $possibleCustomHelms);
    }
}