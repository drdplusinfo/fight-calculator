<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web;

use DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CustomArmamentsState;
use DrdPlus\AttackSkeleton\AttackRequest;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomRangedWeaponBody;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\ItemHoldingCode;

class RangedWeaponBody extends AbstractArmamentBody
{
    private \DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomRangedWeaponBody $addCustomRangedWeaponBody;
    private \DrdPlus\AttackSkeleton\CustomArmamentsState $customArmamentsState;
    private \DrdPlus\AttackSkeleton\CurrentArmamentsValues $currentArmamentsValues;
    private \DrdPlus\AttackSkeleton\CurrentArmaments $currentArmaments;
    private \DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages $armamentsUsabilityMessages;
    private \DrdPlus\AttackSkeleton\HtmlHelper $htmlHelper;
    private \DrdPlus\AttackSkeleton\PossibleArmaments $possibleArmaments;

    public function __construct(
        CustomArmamentsState $customArmamentsState,
        CurrentArmamentsValues $currentArmamentsValues,
        CurrentArmaments $currentArmaments,
        PossibleArmaments $possibleArmaments,
        ArmamentsUsabilityMessages $armamentsUsabilityMessages,
        HtmlHelper $htmlHelper,
        AddCustomRangedWeaponBody $addCustomRangedWeaponBody
    )
    {
        $this->customArmamentsState = $customArmamentsState;
        $this->currentArmamentsValues = $currentArmamentsValues;
        $this->currentArmaments = $currentArmaments;
        $this->armamentsUsabilityMessages = $armamentsUsabilityMessages;
        $this->htmlHelper = $htmlHelper;
        $this->possibleArmaments = $possibleArmaments;
        $this->addCustomRangedWeaponBody = $addCustomRangedWeaponBody;
    }

    public function getValue(): string
    {
        return <<<HTML
{$this->getAddCustomRangedWeapon()}
{$this->getCurrentCustomRangedWeapons()}
<div class="{$this->getVisibilityClass()}">
    <div class="row messages">
      {$this->getMessagesAboutRangedWeapons()}
    </div>
    <div class="row" id="chooseRangedWeapon">
        <div class="col">
          {$this->addCustomRangedWeapon()}
          {$this->getRangedWeaponHolding()}
        </div>
    </div>
</div>
HTML;
    }

    private function addCustomRangedWeapon(): string
    {
        return <<<HTML
<a title="Přidat vlastní zbraň na dálku" href="{$this->getUrlToAddNewRangedWeapon()}" class="btn btn-success btn-sm add">+</a>
<label>
    <select name="{$this->getRangedWeaponSelectName()}" title="Zbraň na dálku">
        {$this->getPossibleRangedWeapons()}
    </select>
</label>
HTML;
    }

    private function getRangedWeaponHolding(): string
    {
        return <<<HTML
<label>
    <input type="radio" value="{$this->getMainHandHolding()}" name="{$this->getRangedWeaponHoldingName()}" {$this->getCheckedMainHandHolding()}>
    v dominantní ruce
</label>
<label>
    <input type="radio" value="{$this->getOffhandHolding()}" name="{$this->getRangedWeaponHoldingName()}" {$this->getCheckedOffhandHolding()}>
    v druhé ruce
</label>
<label>
    <input type="radio" value="{$this->getTwoHandsHolding()}"
           name="{$this->getRangedWeaponHoldingName()}" {$this->getCheckedTwoHandsHolding()}>
    obouručně
</label>
HTML;
    }

    private function getAddCustomRangedWeapon(): string
    {
        if (!$this->customArmamentsState->isAddingNewRangedWeapon()) {
            return '';
        }

        return <<<HTML
<div id="addRangedWeapon" class="row add">
  {$this->addCustomRangedWeaponBody->getValue()}
</div>
HTML;
    }

    private function getCurrentCustomRangedWeapons(): string
    {
        $currentCustomRangedWeapons = '';
        foreach ($this->currentArmamentsValues->getCurrentCustomRangedWeaponsValues() as $weaponName => $weaponValues) {
            /** @var array|string[] $weaponValues */
            foreach ($weaponValues as $typeName => $weaponValue) {
                $currentCustomRangedWeapons .= <<<HTML
<input type="hidden" name="{$typeName}[{$weaponName}]" value="{$weaponValue}">
HTML;
            }
        }

        return $currentCustomRangedWeapons;
    }

    private function getVisibilityClass(): string
    {
        return $this->customArmamentsState->isAddingNewRangedWeapon()
            ? 'hidden'
            : '';
    }

    private function getMessagesAboutRangedWeapons(): string
    {
        $messagesAboutRangedWeapons = '';
        foreach ($this->armamentsUsabilityMessages->getMessagesAboutRangedWeapons() as $messageAboutRangedWeapon) {
            $messagesAboutRangedWeapons .= <<<HTML
<div class="alert alert-primary">{$messageAboutRangedWeapon}</div>
HTML;
        }

        return $messagesAboutRangedWeapons;
    }

    private function getUrlToAddNewRangedWeapon(): string
    {
        return $this->htmlHelper->getLocalUrlToAction(AttackRequest::ADD_NEW_RANGED_WEAPON);
    }

    private function getRangedWeaponSelectName(): string
    {
        return AttackRequest::RANGED_WEAPON;
    }

    private function getTranslatedWeaponCategory(string $weaponCategory): string
    {
        return WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2);
    }

    private function getPossibleRangedWeaponsOfCategory(array $rangedWeaponsFromCategory): string
    {
        $possibleRangedWeaponsOfCategory = '';
        /** @var array $rangedWeapon */
        foreach ($rangedWeaponsFromCategory as $rangedWeapon) {
            /** @var RangedWeaponCode $rangedWeaponCode */
            $rangedWeaponCode = $rangedWeapon['code'];
            $possibleRangedWeaponsOfCategory .= <<<HTML
<option value="{$rangedWeaponCode->getValue()}" {$this->getRangedWeaponSelected($rangedWeaponCode)} {$this->htmlHelper->getDisabled($rangedWeapon['canUseIt'])}>
  {$this->getUsabilityPictogram($rangedWeapon['canUseIt'])}{$rangedWeaponCode->translateTo('cs')}
</option>
HTML;
        }

        return $possibleRangedWeaponsOfCategory;
    }

    private function getRangedWeaponSelected(RangedWeaponCode $rangedWeaponCode): string
    {
        return $this->htmlHelper->getSelected($this->currentArmaments->getCurrentRangedWeapon(), $rangedWeaponCode);
    }

    private function getPossibleRangedWeapons(): string
    {
        $possibleRangedWeapons = '';
        /** @var array $rangedWeaponsFromCategory */
        foreach ($this->possibleArmaments->getPossibleRangedWeapons() as $weaponCategory => $rangedWeaponsFromCategory) {
            $possibleRangedWeapons .= <<<HTML
<optgroup label="{$this->getTranslatedWeaponCategory($weaponCategory)}">
    {$this->getPossibleRangedWeaponsOfCategory($rangedWeaponsFromCategory)}
</optgroup>
HTML;
        }

        return $possibleRangedWeapons;
    }

    private function getMainHandHolding(): string
    {
        return ItemHoldingCode::MAIN_HAND;
    }

    private function getRangedWeaponHoldingName(): string
    {
        return AttackRequest::RANGED_WEAPON_HOLDING;
    }

    private function getCheckedMainHandHolding(): string
    {
        return $this->getCheckedHolding(ItemHoldingCode::MAIN_HAND);
    }

    private function getCheckedHolding(string $holdingToCheck): string
    {
        return $this->currentArmaments->getCurrentRangedWeaponHolding()->getValue() === $holdingToCheck
            ? 'checked'
            : '';
    }

    private function getCheckedOffhandHolding(): string
    {
        return $this->getCheckedHolding(ItemHoldingCode::OFFHAND);
    }

    private function getCheckedTwoHandsHolding(): string
    {
        return $this->getCheckedHolding(ItemHoldingCode::TWO_HANDS);
    }

    private function getOffhandHolding(): string
    {
        return ItemHoldingCode::OFFHAND;
    }

    private function getTwoHandsHolding(): string
    {
        return ItemHoldingCode::TWO_HANDS;
    }
}