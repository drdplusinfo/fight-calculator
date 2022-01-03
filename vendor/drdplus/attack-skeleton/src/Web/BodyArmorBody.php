<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CustomArmamentsState;
use DrdPlus\AttackSkeleton\AttackRequest;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomBodyArmorBody;
use DrdPlus\Codes\Armaments\BodyArmorCode;

class BodyArmorBody extends AbstractArmamentBody
{
    private \DrdPlus\AttackSkeleton\CustomArmamentsState $customArmamentsState;
    private \DrdPlus\AttackSkeleton\CurrentArmamentsValues $currentArmamentsValues;
    private \DrdPlus\AttackSkeleton\CurrentArmaments $currentArmaments;
    private \DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages $armamentsUsabilityMessages;
    private \DrdPlus\AttackSkeleton\HtmlHelper $htmlHelper;
    private \DrdPlus\AttackSkeleton\PossibleArmaments $possibleArmaments;
    private \DrdPlus\Armourer\Armourer $armourer;
    private \DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomBodyArmorBody $addCustomBodyArmorBody;

    public function __construct(
        CustomArmamentsState $customArmamentsState,
        CurrentArmamentsValues $currentArmamentsValues,
        CurrentArmaments $currentArmaments,
        PossibleArmaments $possibleArmaments,
        ArmamentsUsabilityMessages $armamentsUsabilityMessages,
        HtmlHelper $htmlHelper,
        Armourer $armourer,
        AddCustomBodyArmorBody $addCustomBodyArmorBody
    )
    {
        $this->customArmamentsState = $customArmamentsState;
        $this->currentArmamentsValues = $currentArmamentsValues;
        $this->currentArmaments = $currentArmaments;
        $this->armamentsUsabilityMessages = $armamentsUsabilityMessages;
        $this->htmlHelper = $htmlHelper;
        $this->possibleArmaments = $possibleArmaments;
        $this->armourer = $armourer;
        $this->addCustomBodyArmorBody = $addCustomBodyArmorBody;
    }

    public function getValue(): string
    {

        return <<<HTML
{$this->getAddCustomBodyArmor()}
{$this->getCurrentCustomBodyArmors()}
<div class="row {$this->getVisibilityClass()}" id="chooseBodyArmor">
  <div class="col">
    <div class="messages">
      {$this->getMessagesAboutBodyArmors()}
    </div>
    <a title="Přidat vlastní zbroj" href="{$this->getUrlToAddNewBodyArmor()}" class="btn btn-success btn-sm add">+</a>
    <label>
      <select name="{$this->getBodyArmorSelectName()}" title="Zbroj">
        {$this->getPossibleBodyArmors()}
      </select>
    </label>
  </div>
</div>
HTML;
    }

    private function getAddCustomBodyArmor(): string
    {
        if (!$this->customArmamentsState->isAddingNewBodyArmor()) {
            return '';
        }

        return <<<HTML
<div id="addBodyArmor" class="row add">
  {$this->addCustomBodyArmorBody->getValue()}
</div>
HTML;
    }

    private function getCurrentCustomBodyArmors(): string
    {
        $possibleCustomBodyArmors = [];
        foreach ($this->currentArmamentsValues->getCurrentCustomBodyArmorsValues() as $armorName => $armorValues) {
            /** @var array|string[] $armorValues */
            foreach ($armorValues as $typeName => $armorValue) {
                $possibleCustomBodyArmors[] = <<<HTML
<input type="hidden" name="{$typeName}[{$armorName}]" value="{$armorValue}">
HTML;
            }
        }

        return implode("\n", $possibleCustomBodyArmors);
    }

    private function getBodyArmorSelectName(): string
    {
        return AttackRequest::BODY_ARMOR;
    }

    private function getVisibilityClass(): string
    {
        return $this->customArmamentsState->isAddingNewBodyArmor()
            ? 'hidden'
            : '';
    }

    private function getMessagesAboutBodyArmors(): string
    {
        $messagesAboutBodyArmors = [];
        foreach ($this->armamentsUsabilityMessages->getMessagesAboutBodyArmors() as $messageAboutBodyArmor) {
            $messagesAboutBodyArmors[] = <<<HTML
<div class="alert alert-primary">{$messageAboutBodyArmor}</div>
HTML;
        }

        return implode("\n", $messagesAboutBodyArmors);
    }

    private function getUrlToAddNewBodyArmor(): string
    {
        return $this->htmlHelper->getLocalUrlWithQuery([AttackRequest::ACTION => AttackRequest::ADD_NEW_BODY_ARMOR]);
    }

    private function getPossibleBodyArmors(): string
    {
        $bodyArmors = [];
        /** @var mixed[] $possibleBodyArmor */
        foreach ($this->possibleArmaments->getPossibleBodyArmors() as $possibleBodyArmor) {
            /** @var BodyArmorCode $bodyArmorCode */
            $bodyArmorCode = $possibleBodyArmor['code'];
            $bodyArmors[] = <<<HTML
<option value="{$bodyArmorCode->getValue()}" {$this->getBodyArmorSelected($bodyArmorCode)} {$this->htmlHelper->getDisabled($possibleBodyArmor['canUseIt'])}>
  {$this->getUsabilityPictogram($possibleBodyArmor['canUseIt'])}{$bodyArmorCode->translateTo('cs')} {$this->getBodyArmorProtection($bodyArmorCode)}
</option>
HTML;
        }

        return implode("\n", $bodyArmors);
    }

    private function getBodyArmorSelected(BodyArmorCode $bodyArmorCode): string
    {
        return $this->htmlHelper->getSelected($this->currentArmaments->getCurrentBodyArmor(), $bodyArmorCode);
    }

    private function getBodyArmorProtection(BodyArmorCode $bodyArmorCode): string
    {
        return $this->htmlHelper->formatInteger($this->armourer->getProtectionOfBodyArmor($bodyArmorCode));
    }
}
