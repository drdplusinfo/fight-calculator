<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web;

use DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CustomArmamentsState;
use DrdPlus\AttackSkeleton\AttackRequest;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomMeleeWeaponBody;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\ItemHoldingCode;

class MeleeWeaponBody extends AbstractArmamentBody
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
    /** @var AddCustomMeleeWeaponBody */
    private $addCustomMeleeWeaponBody;

    public function __construct(
        CustomArmamentsState $customArmamentsState,
        CurrentArmamentsValues $currentArmamentsValues,
        CurrentArmaments $currentArmaments,
        PossibleArmaments $possibleArmaments,
        ArmamentsUsabilityMessages $armamentsUsabilityMessages,
        HtmlHelper $htmlHelper,
        AddCustomMeleeWeaponBody $addCustomMeleeWeaponBody
    )
    {
        $this->customArmamentsState = $customArmamentsState;
        $this->currentArmamentsValues = $currentArmamentsValues;
        $this->currentArmaments = $currentArmaments;
        $this->armamentsUsabilityMessages = $armamentsUsabilityMessages;
        $this->htmlHelper = $htmlHelper;
        $this->possibleArmaments = $possibleArmaments;
        $this->addCustomMeleeWeaponBody = $addCustomMeleeWeaponBody;
    }

    private function getAddCustomMeleeWeapon(): string
    {
        if (!$this->customArmamentsState->isAddingNewMeleeWeapon()) {
            return '';
        }

        return <<<HTML
<div id="addMeleeWeapon" class="row add">
  {$this->addCustomMeleeWeaponBody->getValue()}
</div>
HTML;
    }

    private function getCurrentCustomMeleeWeapons(): string
    {
        $currentCustomMeleeWeapons = '';
        foreach ($this->currentArmamentsValues->getCurrentCustomMeleeWeaponsValues() as $weaponName => $weaponValues) {
            /** @var array|string[] $weaponValues */
            foreach ($weaponValues as $typeName => $weaponValue) {
                $currentCustomMeleeWeapons .= <<<HTML
<input type="hidden" name="{$typeName}[{$weaponName}]" value="{$weaponValue}">
HTML;
            }
        }

        return $currentCustomMeleeWeapons;
    }

    private function getVisibilityClass(): string
    {
        return $this->customArmamentsState->isAddingNewMeleeWeapon()
            ? 'hidden'
            : '';
    }

    private function getMessagesAboutMeleeWeapons(): string
    {
        $messagesAboutMeleeWeapons = '';
        foreach ($this->armamentsUsabilityMessages->getMessagesAboutMeleeWeapons() as $messageAboutMeleeWeapon) {
            $messagesAboutMeleeWeapons .= <<<HTML
<div class="alert alert-primary">{$messageAboutMeleeWeapon}</div>
HTML;
        }

        return $messagesAboutMeleeWeapons;
    }

    private function getUrlToAddNewMeleeWeapon(): string
    {
        return $this->htmlHelper->getLocalUrlToAction(AttackRequest::ADD_NEW_MELEE_WEAPON);
    }

    private function getMeleeWeaponSelectName(): string
    {
        return AttackRequest::MELEE_WEAPON;
    }

    private function getTranslatedWeaponCategory(string $weaponCategory): string
    {
        return WeaponCategoryCode::getIt($weaponCategory)->translateTo('cs', 2);
    }

    private function getPossibleMeleeWeaponsOfCategory(array $meleeWeaponsFromCategory): string
    {
        $possibleMeleeWeaponsOfCategory = '';
        /** @var array $meleeWeapon */
        foreach ($meleeWeaponsFromCategory as $meleeWeapon) {
            /** @var MeleeWeaponCode $meleeWeaponCode */
            $meleeWeaponCode = $meleeWeapon['code'];
            $possibleMeleeWeaponsOfCategory .= <<<HTML
<option value="{$meleeWeaponCode->getValue()}" {$this->getMeleeWeaponSelected($meleeWeaponCode)} {$this->htmlHelper->getDisabled($meleeWeapon['canUseIt'])}>
  {$this->getUsabilityPictogram($meleeWeapon['canUseIt'])}{$meleeWeaponCode->translateTo('cs')}
</option>
HTML;
        }

        return $possibleMeleeWeaponsOfCategory;
    }

    private function getMeleeWeaponSelected(MeleeWeaponCode $meleeWeaponCode): string
    {
        return $this->htmlHelper->getSelected($this->currentArmaments->getCurrentMeleeWeapon(), $meleeWeaponCode);
    }

    private function getPossibleMeleeWeapons(): string
    {
        $possibleMeleeWeapons = '';
        /** @var array $meleeWeaponsFromCategory */
        foreach ($this->possibleArmaments->getPossibleMeleeWeapons() as $weaponCategory => $meleeWeaponsFromCategory) {
            $possibleMeleeWeapons .= <<<HTML
<optgroup label="{$this->getTranslatedWeaponCategory($weaponCategory)}">
    {$this->getPossibleMeleeWeaponsOfCategory($meleeWeaponsFromCategory)}
</optgroup>
HTML;
        }

        return $possibleMeleeWeapons;
    }

    private function getMainHandHolding(): string
    {
        return ItemHoldingCode::MAIN_HAND;
    }

    private function getMeleeWeaponHoldingName(): string
    {
        return AttackRequest::MELEE_WEAPON_HOLDING;
    }

    private function getMainHandHoldingChecked(): string
    {
        return $this->getHoldingChecked(ItemHoldingCode::MAIN_HAND);
    }

    private function getHoldingChecked(string $holdingToCheck): string
    {
        return $this->currentArmaments->getCurrentMeleeWeaponHolding()->getValue() === $holdingToCheck
            ? 'checked'
            : '';
    }

    private function getOffhandHoldingChecked(): string
    {
        return $this->getHoldingChecked(ItemHoldingCode::OFFHAND);
    }

    private function getTwoHandsHoldingChecked(): string
    {
        return $this->getHoldingChecked(ItemHoldingCode::TWO_HANDS);
    }

    private function getOffhandHolding(): string
    {
        return ItemHoldingCode::OFFHAND;
    }

    private function getTwoHandsHolding(): string
    {
        return ItemHoldingCode::TWO_HANDS;
    }

    public function getValue(): string
    {
        return <<<HTML
{$this->getAddCustomMeleeWeapon()}
{$this->getCurrentCustomMeleeWeapons()}
<div class="{$this->getVisibilityClass()}">
    <div class="row messages">
      {$this->getMessagesAboutMeleeWeapons()}
    </div>
    <div class="row" id="chooseMeleeWeapon">
        <div class="col">
            <a title="Přidat vlastní zbraň na blízko" href="{$this->getUrlToAddNewMeleeWeapon()}" class="button add">+</a>
            <label>
                <select name="{$this->getMeleeWeaponSelectName()}" title="Zbraň na blízko">
                    {$this->getPossibleMeleeWeapons()}
                </select>
            </label>
        </div>
        <div class="col">
            <label>
                <input type="radio" value="{$this->getMainHandHolding()}" name="{$this->getMeleeWeaponHoldingName()}" {$this->getMainHandHoldingChecked()}>
                v dominantní ruce
            </label>
        </div>
        <div class="col">
            <label>
                <input type="radio" value="{$this->getOffhandHolding()}" name="{$this->getMeleeWeaponHoldingName()}" {$this->getOffhandHoldingChecked()}>
                v druhé ruce
            </label>
        </div>
        <div class="col">
            <label>
                <input type="radio" value="{$this->getTwoHandsHolding()}"
                       name="{$this->getMeleeWeaponHoldingName()}" {$this->getTwoHandsHoldingChecked()}>
                obouručně
            </label>
        </div>
    </div>
</div>
HTML;
    }
}