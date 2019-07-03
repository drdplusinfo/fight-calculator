<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web\AddCustomArmament;

use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class AddCustomMeleeWeaponBody extends StrictObject implements BodyInterface
{
    use WeaponWoundTypesTrait;
    use CancelActionButtonTrait;

    /** @var HtmlHelper */
    private $frontendHelper;

    public function __construct(HtmlHelper $frontendHelper)
    {
        $this->frontendHelper = $frontendHelper;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return <<<HTML
<div class="col">
  <label>Název
    <input type="text" placeholder="Název nové zbraně na blízko" name="{$this->getCustomMeleeWeaponName()}[0]" required>
  </label>
  <label>Kategorie
    <select name="{$this->getCustomMeleeWeaponCategoryName()}[0]" required>
      {$this->getMeleeWeaponCategories()}
    </select>
  </label>
  <label>Potřebná síla
    <input type="number" min="-20" max="50" value="0" name="{$this->getCustomMeleeWeaponRequiredStrengthName()}[0]" required>
  </label>
  <label>Délka
    <input type="number" min="0" max="10" value="1" name="{$this->getCustomMeleeWeaponLengthName()}[0]" required>
  </label>
  <label>Útočnost
    <input type="number" min="=-20" max="50" value="0" name="{$this->getCustomMeleeWeaponOffensivenessName()}[0]" required>
  </label>
  <label>Zranění
    <input type="number" min="=-20" max="50" value="0" name="{$this->getCustomMeleeWeaponWoundsName()}[0]" required>
  </label>
  <label>Typ
    <select name="{$this->getCustomMeleeWeaponWoundTypeName()}[0]" required>
      {$this->getWeaponWoundTypes()}
    </select>
  </label>
  <label>Kryt
    <input type="number" min="-10" max="20" value="0" name="{$this->getCustomMeleeWeaponCoverName()}[0]" required>
  </label>
  <label>Váha v kg
    <input type="number" min="0" max="99.99" step="0.1" value="1" name="{$this->getCustomMeleeWeaponWeightName()}[0]" required>
  </label>
  <label>Pouze obouruční
    <input type="checkbox" value="1" name="{$this->getCustomMeleeWeaponTwoHandedOnlyName()}[0]">
  </label>
  <input type="submit" class="manual" value="Přidat">
</div>
{$this->getCancelActionButton($this->frontendHelper)}
HTML;
    }

    private function getCustomMeleeWeaponTwoHandedOnlyName(): string
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_TWO_HANDED_ONLY;
    }

    private function getCustomMeleeWeaponWeightName()
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_WEIGHT;
    }

    private function getCustomMeleeWeaponName(): string
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_NAME;
    }

    private function getCustomMeleeWeaponCategoryName(): string
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_CATEGORY;
    }

    private function getMeleeWeaponCategories(): string
    {
        $possibleMeleeWeaponCategories = '';
        foreach (WeaponCategoryCode::getMeleeWeaponCategoryValues() as $meleeWeaponCategoryValue) {
            $weaponCategory = WeaponCategoryCode::getIt($meleeWeaponCategoryValue);
            $possibleMeleeWeaponCategories .= <<<HTML
<option value="{$meleeWeaponCategoryValue}"><{$weaponCategory->translateTo('cs')}</option>
HTML;
        }

        return $possibleMeleeWeaponCategories;
    }

    private function getCustomMeleeWeaponRequiredStrengthName(): string
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_REQUIRED_STRENGTH;
    }

    private function getCustomMeleeWeaponLengthName(): string
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_LENGTH;
    }

    private function getCustomMeleeWeaponOffensivenessName(): string
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_OFFENSIVENESS;
    }

    private function getCustomMeleeWeaponWoundsName(): string
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_WOUNDS;
    }

    private function getCustomMeleeWeaponWoundTypeName(): string
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_WOUND_TYPE;
    }

    private function getCustomMeleeWeaponCoverName(): string
    {
        return CurrentArmamentsValues::CUSTOM_MELEE_WEAPON_COVER;
    }
}