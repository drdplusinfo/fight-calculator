<?php
declare(strict_types=1);

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
        return <<<HTML
<div class="col fight-property">
  BČ
  <img alt="Bojové číslo" class="line-sized" src="/images/emojione/fight-2694.png">
  <span class="{$this->getCssClassForChangeOfFightNumber()}">{$this->currentFightProperties->getFightNumber()}</span>
</div>
<div class="col fight-property">
  ÚČ
  <img alt="Útočné číslo" class="line-sized" src="/images/emojione/fight-number-1f624.png">
  <span class="{$this->getCssClassForChangeOfAttackNumber()}">
    {$this->currentFightProperties->getAttackNumber($this->fight->getCurrentTargetDistance(), $this->fight->getCurrentTargetSize())}
  </span>
</div>
<div class="col fight-property">
  ZZ
  <img alt="Základ zranění" class="line-sized" src="/images/emojione/base-of-wounds-1f480.png">
  <span class="{$this->getCssClassForChangeOfBaseOfWounds()}">{$this->currentFightProperties->getBaseOfWounds()}</span>
</div>
<div class="col fight-property">
  OČ
  <img alt="Obranné číslo" class="line-sized" src="/images/emojione/defense-number-1f6e1.png">
  <span class="{$this->getCssClassForChangeOfDefenseNumber()}">{$this->currentFightProperties->getDefenseNumberWithWeaponlike()}</span>
</div>
HTML;
    }

    private function getCssClassForChangeOfFightNumber(): string
    {
        return $this->htmlHelper->getCssClassForChangedValue(
            $this->previousFightProperties->getFightNumber(),
            $this->currentFightProperties->getFightNumber()
        );
    }

    private function getCssClassForChangeOfAttackNumber(): string
    {
        return $this->htmlHelper->getCssClassForChangedValue(
            $this->previousFightProperties->getAttackNumber(
                $this->fight->getPreviousTargetDistance(),
                $this->fight->getPreviousTargetSize()
            ),
            $this->currentFightProperties->getAttackNumber(
                $this->fight->getCurrentTargetDistance(),
                $this->fight->getCurrentTargetSize()
            )
        );
    }

    private function getCssClassForChangeOfBaseOfWounds(): string
    {
        return $this->htmlHelper->getCssClassForChangedValue(
            $this->previousFightProperties->getBaseOfWounds(),
            $this->currentFightProperties->getBaseOfWounds()
        );
    }

    private function getCssClassForChangeOfDefenseNumber(): string
    {
        return $this->htmlHelper->getCssClassForChangedValue(
            $this->previousFightProperties->getDefenseNumberWithWeaponlike(),
            $this->currentFightProperties->getDefenseNumberWithWeaponlike()
        );
    }

}