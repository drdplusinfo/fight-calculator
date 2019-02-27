<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\FightCalculator\Fight;
use DrdPlus\FightProperties\FightProperties;

class ShieldFightPropertiesBody extends FightPropertiesBody
{
    /** @var ItemHoldingCode */
    private $currentShieldHolding;
    /** @var ItemHoldingCode */
    private $previousShieldHolding;
    /** @var ShieldCode */
    private $currentShield;
    /** @var ShieldCode */
    private $selectedShield;

    public function __construct(
        ItemHoldingCode $currentShieldHolding,
        ItemHoldingCode $previousShieldHolding,
        FightProperties $currentFightProperties,
        FightProperties $previousFightProperties,
        Fight $fight,
        HtmlHelper $htmlHelper,
        ShieldCode $currentShield,
        ShieldCode $selectedShield
    )
    {
        parent::__construct($currentFightProperties, $previousFightProperties, $fight, $htmlHelper);
        $this->currentShieldHolding = $currentShieldHolding;
        $this->previousShieldHolding = $previousShieldHolding;
        $this->currentShield = $currentShield;
        $this->selectedShield = $selectedShield;
    }

    public function getValue(): string
    {
        $shieldFightProperties = parent::getValue();
        return <<<HTML
{$this->getUnusableShieldWarning()}
<div class="row">
{$shieldFightProperties}
<div class="col note">
  držen
  <span class="keyword {$this->getCssClassForChangeOfShieldHolding()}">
      {$this->getCurrentShieldHoldingHumanName()}
  </span>
</div>
</div>
HTML;
    }

    private function getUnusableShieldWarning(): string
    {
        if (!$this->selectedShield->isUnarmed() && $this->currentShield->isUnarmed() && $this->currentShieldHolding->holdsByOffhand()) {
            return <<<HTML
<div class="row">
<div class="col">
<div class="alert alert-secondary">
Štít v téhle ruce neudržíš, přehoď si zbraň do druhé ruky
</div>
</div>
</div>
HTML;
        }
        return '';
    }

    private function getCssClassForChangeOfShieldHolding(): string
    {
        return $this->previousShieldHolding->getValue() !== $this->currentShieldHolding->getValue()
            ? 'changed'
            : '';
    }

    private function getCurrentShieldHoldingHumanName(): string
    {
        return $this->currentShieldHolding->translateTo('cs');
    }
}