<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web\AddCustomArmament;

use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class AddCustomRangedWeaponBody extends StrictObject implements BodyInterface
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
    <input type="text" placeholder="Název nové zbraně na dálku" name="{$this->getCustomRangedWeaponName()}[0]" required>
  </label>
  <label>Kategorie
    <select name="{$this->getCustomRangedWeaponCategoryName()}[0]" required>
      {$this->getRangedWeaponCategories()}
    </select>
  </label>
  <label>Potřebná síla
    <input type="number" min="-20" max="50" value="0" name="{$this->getCustomRangedWeaponRequiredStrengthName()}[0]" required>
  </label>
  <label>Dostřel v metrech
    <input type="number" min="0" max="500" value="1" name="{$this->getCustomRangedWeaponRangeInMetersName()}[0]" required>
  </label>
  <label>Útočnost
    <input type="number" min="=-20" max="50" value="0" name="{$this->getCustomRangedWeaponOffensivenessName()}[0]" required>
  </label>
  <label>Zranění
    <input type="number" min="=-20" max="50" value="0" name="{$this->getCustomRangedWeaponWoundsName()}[0]" required>
  </label>
  <label>Typ
    <select name="{$this->getCustomRangedWeaponWoundTypeName()}[0]" required>
      {$this->getWeaponWoundTypes()}
    </select>
  </label>
  <label>Kryt
    <input type="number" min="-10" max="20" value="0" name="{$this->getCustomRangedWeaponCoverName()}[0]" required>
  </label>
  <label>Váha v kg
    <input type="number" min="0" max="99.99" value="1" name="{$this->getCustomRangedWeaponWeightName()}[0]" required>
  </label>
  <label>Pouze obouruční
    <input type="checkbox" value="1" name="{$this->getCustomRangedWeaponTwoHandedOnlyName()}[0]"></label>
  <input type="submit" class="manual" value="Přidat">
</div>
{$this->getCancelActionButton($this->frontendHelper)}
HTML;
    }

    private function getCustomRangedWeaponTwoHandedOnlyName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_TWO_HANDED_ONLY;
    }

    private function getCustomRangedWeaponWeightName()
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_WEIGHT;
    }

    private function getCustomRangedWeaponName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_NAME;
    }

    private function getCustomRangedWeaponCategoryName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_CATEGORY;
    }

    private function getRangedWeaponCategories(): string
    {
        $possibleRangedWeaponCategories = '';
        foreach (WeaponCategoryCode::getRangedWeaponCategoryValues() as $meleeWeaponCategoryValue) {
            $weaponCategory = WeaponCategoryCode::getIt($meleeWeaponCategoryValue);
            $possibleRangedWeaponCategories .= <<<HTML
<option value="{$meleeWeaponCategoryValue}"><{$weaponCategory->translateTo('cs')}</option>
HTML;
        }

        return $possibleRangedWeaponCategories;
    }

    private function getCustomRangedWeaponRequiredStrengthName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_REQUIRED_STRENGTH;
    }

    private function getCustomRangedWeaponRangeInMetersName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_RANGE_IN_M;
    }

    private function getCustomRangedWeaponOffensivenessName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_OFFENSIVENESS;
    }

    private function getCustomRangedWeaponWoundsName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_WOUNDS;
    }

    private function getCustomRangedWeaponWoundTypeName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_WOUND_TYPE;
    }

    private function getCustomRangedWeaponCoverName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_COVER;
    }
}