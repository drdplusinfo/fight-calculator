<?php declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\FightCalculator\Fight;
use DrdPlus\FightProperties\FightProperties;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class FightPropertiesBody extends StrictObject implements BodyInterface
{
    /** @var FightProperties */
    private $currentFightProperties;
    /** @var FightProperties */
    private $previousFightProperties;
    /** @var Fight */
    private $fight;
    /** @var HtmlHelper */
    private $htmlHelper;

    public function __construct(
        FightProperties $currentFightProperties,
        FightProperties $previousFightProperties,
        Fight $fight,
        HtmlHelper $htmlHelper
    )
    {
        $this->currentFightProperties = $currentFightProperties;
        $this->previousFightProperties = $previousFightProperties;
        $this->fight = $fight;
        $this->htmlHelper = $htmlHelper;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        $previousFightNumber = $this->previousFightProperties->getFightNumber();
        $currentFightNumber = $this->currentFightProperties->getFightNumber();
        $previousAttackNumber = $this->previousFightProperties->getAttackNumber(
            $this->fight->getPreviousTargetDistance(),
            $this->fight->getPreviousTargetSize()
        );
        $currentAttackNumber = $this->currentFightProperties->getAttackNumber(
            $this->fight->getCurrentTargetDistance(),
            $this->fight->getCurrentTargetSize()
        );
        $previousBaseOfWounds = $this->previousFightProperties->getBaseOfWounds();
        $currentBaseOfWounds = $this->currentFightProperties->getBaseOfWounds();
        $previousDefenseNumber = $this->previousFightProperties->getDefenseNumberWithWeaponlike();
        $currentDefenseNumber = $this->currentFightProperties->getDefenseNumberWithWeaponlike();
        return <<<HTML
<div class="col fight-property">
  BČ
  <span class="{$this->htmlHelper->getCssClassForChangedValue($previousFightNumber, $currentFightNumber)}">
    {$this->htmlHelper->formatInteger($currentFightNumber)}
  </span>
</div>
<div class="col fight-property">
  ÚČ
  <span class="{$this->htmlHelper->getCssClassForChangedValue($previousAttackNumber, $currentAttackNumber)}">
    {$this->htmlHelper->formatInteger($currentAttackNumber)}
  </span>
</div>
<div class="col fight-property">
  ZZ
  <span class="{$this->htmlHelper->getCssClassForChangedValue($previousBaseOfWounds, $currentBaseOfWounds)}">
    {$this->htmlHelper->formatInteger($currentBaseOfWounds)}
  </span>
</div>
<div class="col fight-property">
  OČ
  <span class="{$this->htmlHelper->getCssClassForChangedValue($previousDefenseNumber, $currentDefenseNumber)}">
    {$this->htmlHelper->formatInteger($currentDefenseNumber)}
  </span>
</div>
HTML;
    }
}