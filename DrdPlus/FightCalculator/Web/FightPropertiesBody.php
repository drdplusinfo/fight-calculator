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
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;
    /**
     * @var Fight
     */
    private $fight;
    /**
     * @var FightProperties
     */
    private $currentFightProperties;
    /**
     * @var FightProperties
     */
    private $previousFightProperties;

    public function __construct(
        HtmlHelper $htmlHelper,
        FightProperties $currentFightProperties,
        FightProperties $previousFightProperties
    )
    {

        $this->htmlHelper = $htmlHelper;
        $this->currentFightProperties = $currentFightProperties;
        $this->previousFightProperties = $previousFightProperties;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return <<<HTML
<div class="col">
  BČ
  <img alt="Bojové číslo" class="line-sized" src="/images/emojione/fight-2694.png">
  <span class="{$this->getChangeClassForFightNumber()}">{$this->currentFightProperties->getFightNumber()}</span>
</div>
<div class="col">
  ÚČ
  <img alt="Útočné číslo" class="line-sized" src="/images/emojione/fight-number-1f624.png">
  <span class="{$this->getChangeClassForAttackNumber()}">
    {$this->currentFightProperties->getAttackNumber($this->fight->getCurrentTargetDistance(), $this->fight->getCurrentTargetSize())}
  </span>
</div>
<div class="col">
  ZZ
  <img alt="Základ zranění" class="line-sized" src="/images/emojione/base-of-wounds-1f480.png">
  <span class="{$this->getChangeClassForBaseOfWounds()}">{$this->currentFightProperties->getBaseOfWounds()}</span>
</div>
<div class="col">
  OČ
  <img alt="Obranné číslo" class="line-sized" src="/images/emojione/defense-number-1f6e1.png">
  <span class="{$this->getChangeClassForDefenseNumber()}">{$this->currentFightProperties->getDefenseNumberWithWeaponlike()}</span>
</div>
HTML;
    }

    private function getChangeClassForFightNumber(): string
    {
        return $this->htmlHelper->getCssClassForChangedValue(
            $this->previousFightProperties->getFightNumber(),
            $this->currentFightProperties->getFightNumber()
        );
    }

    private function getChangeClassForAttackNumber(): string
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

    private function getChangeClassForBaseOfWounds(): string
    {
        return $this->htmlHelper->getCssClassForChangedValue(
            $this->previousFightProperties->getBaseOfWounds(),
            $this->currentFightProperties->getBaseOfWounds()
        );
    }

    private function getChangeClassForDefenseNumber(): string
    {
        return $this->htmlHelper->getCssClassForChangedValue(
            $this->previousFightProperties->getDefenseNumberWithWeaponlike(),
            $this->currentFightProperties->getDefenseNumberWithWeaponlike()
        );
    }

}