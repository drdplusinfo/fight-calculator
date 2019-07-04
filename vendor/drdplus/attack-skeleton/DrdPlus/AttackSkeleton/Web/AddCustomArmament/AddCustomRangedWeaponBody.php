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
  <div>
    <label for="customRangedWeaponName">Název</label>
  </div>
    <div>
      <input id="customRangedWeaponName" type="text" placeholder="Název nové zbraně na dálku" name="{$this->getCustomRangedWeaponName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customRangedWeaponCategory">Kategorie</label>
  </div>
    <div>
        <select id="customRangedWeaponCategory" name="{$this->getCustomRangedWeaponCategoryName()}[0]" required>
          {$this->getRangedWeaponCategories()}
        </select>
    </div>
</div>
<div class="col">
      <div>
        <label for="customRangedWeaponRequiredStrength">Potřebná síla</label>
      </div>
    <div>
      <input id="customRangedWeaponRequiredStrength" type="number" min="-20" max="50" value="0" name="{$this->getCustomRangedWeaponRequiredStrengthName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customRangedWeaponRange">Dostřel v metrech</label>
  </div>
    <div>
      <input id="customRangedWeaponRange" type="number" min="0" max="500" value="1" name="{$this->getCustomRangedWeaponRangeInMetersName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customRangedWeaponOffensiveness">Útočnost</label>
  </div>
    <div>
      <input id="customRangedWeaponOffensiveness" type="number" min="=-20" max="50" value="0" name="{$this->getCustomRangedWeaponOffensivenessName()}[0]" required>
    </div>
</div>
<div class="col">
      <div>
        <label for="customRangedWeaponWounds">Zranění</label>
      </div>
    <div>
      <input id="customRangedWeaponWounds" type="number" min="=-20" max="50" value="0" name="{$this->getCustomRangedWeaponWoundsName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customRangedWeaponWoundType">Typ</label>
  </div>
    <div>
        <select id="customRangedWeaponWoundType" name="{$this->getCustomRangedWeaponWoundTypeName()}[0]" required>
          {$this->getWeaponWoundTypes()}
        </select>
    </div>
</div>
<div class="col">
  <div>
    <label for="customRangedWeaponCover">Kryt</label>
  </div>
    <div>
      <input id="customRangedWeaponCover" type="number" min="-10" max="20" value="0" name="{$this->getCustomRangedWeaponCoverName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customRangedWeaponWeight">Váha v kg</label>
  </div>
    <div>
      <input id="customRangedWeaponWeight" type="number" min="0" max="99.99" step="0.1" value="1" name="{$this->getCustomRangedWeaponWeightName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customRangedWeaponTwoHandedOnly">Pouze obouruční</label>
  </div>
    <div>
      <input id="customRangedWeaponTwoHandedOnly" type="checkbox" value="1" name="{$this->getCustomRangedWeaponTwoHandedOnlyName()}[0]">
    </div>
</div>
<div class="col">
  <div>
    <label for="customRangedWeaponMaximalApplicableStrengt">Maximální použitelná síla</label>
  </div>
    <div>
      <input id="customRangedWeaponMaximalApplicableStrength" type="number" min="-20" max="50" value="10" name="{$this->getCustomRangedWeaponMaximalApplicableStrengthName()}[0]" required>
    </div>
</div>
<input type="submit" class="manual" value="Přidat">
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

    private function getCustomRangedWeaponMaximalApplicableStrengthName(): string
    {
        return CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_MAXIMAL_APPLICABLE_STRENGTH;
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