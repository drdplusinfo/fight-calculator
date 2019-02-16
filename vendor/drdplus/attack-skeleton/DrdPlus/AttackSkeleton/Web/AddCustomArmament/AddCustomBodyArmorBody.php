<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web\AddCustomArmament;

use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\HtmlHelper;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class AddCustomBodyArmorBody extends StrictObject implements BodyInterface
{
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
    <input type="text" placeholder="Název nové zbroje" name="{$this->getCustomBodyArmorName()}[0]" required>
  </label>
  <label>Potřebná síla
    <input type="number" min="-20" max="50" value="0" name="{$this->getCustomBodyArmorRequiredStrengthName()}[0]" required>
  </label>
  <label>Omezení
    <input type="number" min="-10" max="20" value="0" name="{$this->getCustomBodyArmorRestrictionName()}[0]" required>
  </label>
  <label>Ochrana
    <input type="number" min="-10" max="20" value="1" name="{$this->getCustomBodyArmorProtectionName()}[0]" required>
  </label>
  <label>Váha v kg
    <input type="number" min="0" max="99.99" value="10" name="{$this->getCustomBodyArmorWeightName()}[0]" required>
  </label>
  <label>Počet kol na obléknutí
    <input type="number" min="0" max="99" value="3" name="{$this->getCustomBodyArmorRoundsToPutOnName()}[0]">
  </label>
  <input type="submit" class="manual" value="Přidat zbroj">
</div>
{$this->getCancelActionButton($this->frontendHelper)}
HTML;
    }

    private function getCustomBodyArmorName(): string
    {
        return CurrentArmamentsValues::CUSTOM_BODY_ARMOR_NAME;
    }

    private function getCustomBodyArmorRequiredStrengthName(): string
    {
        return CurrentArmamentsValues::CUSTOM_BODY_ARMOR_REQUIRED_STRENGTH;
    }

    private function getCustomBodyArmorRestrictionName(): string
    {
        return CurrentArmamentsValues::CUSTOM_BODY_ARMOR_RESTRICTION;
    }

    private function getCustomBodyArmorProtectionName(): string
    {
        return CurrentArmamentsValues::CUSTOM_BODY_ARMOR_PROTECTION;
    }

    private function getCustomBodyArmorWeightName(): string
    {
        return CurrentArmamentsValues::CUSTOM_BODY_ARMOR_WEIGHT;
    }

    private function getCustomBodyArmorRoundsToPutOnName(): string
    {
        return CurrentArmamentsValues::CUSTOM_BODY_ARMOR_ROUNDS_TO_PUT_ON;
    }
}