<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web\AddCustomArmament;

use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\HtmlHelper;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class AddCustomShieldBody extends StrictObject implements BodyInterface
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
  <div>
    <label for="customShieldName">Název</label>
  </div>
    <div>
      <input id="customShieldName" type="text" placeholder="Název nového štítu" name="{$this->getCustomShieldName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customShieldRequiredStrength">Potřebná síla</label>
  </div>
    <div>
      <input id="customShieldRequiredStrength" type="number" min="-20" max="50" value="0" name="{$this->getCustomShieldRequiredStrengthName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customShieldRestriction">Omezení</label>
  </div>
    <div>
      <input id="customShieldRestriction" type="number" min="-10" max="20" value="0" name="{$this->getCustomShieldRestrictionName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customShieldCover">Kryt</label>
  </div>
    <div>
      <input id="customShieldCover" type="number" min="-10" max="20" value="1" name="{$this->getCustomShieldCoverName()}[0]" required>
    </div>
</div>
<div class="col">
  <div>
    <label for="customShieldWeight">Váha v kg</label>
  </div>
    <div>
      <input id="customShieldWeight" type="number" min="0" max="99.99" step="0.1" value="0.5" name="{$this->getCustomShieldWeightName()}[0]" required>
    </div>
</div>
<input type="submit" class="manual" value="Přidat štít">
{$this->getCancelActionButton($this->frontendHelper)}
HTML;
    }

    private function getCustomShieldName(): string
    {
        return CurrentArmamentsValues::CUSTOM_SHIELD_NAME;
    }

    private function getCustomShieldRequiredStrengthName(): string
    {
        return CurrentArmamentsValues::CUSTOM_SHIELD_REQUIRED_STRENGTH;
    }

    private function getCustomShieldRestrictionName(): string
    {
        return CurrentArmamentsValues::CUSTOM_SHIELD_RESTRICTION;
    }

    private function getCustomShieldCoverName(): string
    {
        return CurrentArmamentsValues::CUSTOM_SHIELD_COVER;
    }

    private function getCustomShieldWeightName(): string
    {
        return CurrentArmamentsValues::CUSTOM_SHIELD_WEIGHT;
    }

}