<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\FightCalculator\Fight;
use DrdPlus\FightCalculator\FightRequest;
use DrdPlus\Tables\Tables;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class RangedTargetBody extends StrictObject implements BodyInterface
{
    /**
     * @var Fight
     */
    private $fight;
    /**
     * @var FightPropertiesBody
     */
    private $rangedWeaponFightPropertiesBody;
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;
    /**
     * @var Tables
     */
    private $tables;

    public function __construct(
        Fight $fight,
        FightPropertiesBody $rangedWeaponFightPropertiesBody,
        HtmlHelper $htmlHelper,
        Tables $tables
    )
    {
        $this->fight = $fight;
        $this->rangedWeaponFightPropertiesBody = $rangedWeaponFightPropertiesBody;
        $this->htmlHelper = $htmlHelper;
        $this->tables = $tables;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return <<<HTML
<div class="col">
  <div class="row">
    <div class="col-sm">
      <label>vzdálenost cíle <span class="hint">v metrech</span>
        <input type="number" name="{$this->getRangedTargetDistanceInputName()}" min="1" max="900" step="0.1"
               value="{$this->fight->getCurrentTargetDistance()->getMeters()}">
      </label>
    </div>
    <div class="col-sm">
      <label>velikost cíle <span class="hint">(Vel)</span>
        <input type="number" name="{$this->getRangedTargetSizeInputName()}" value="{$this->fight->getCurrentTargetSize()}">
      </label>
    </div>
  </div>
  <div class="row">
      {$this->rangedWeaponFightPropertiesBody->getValue()}
    <div class="col-sm-3">
      Soubojový dostřel
      <img alt="Luk se šípem" class="line-sized" src="/images/emojione/bow-and-arrow-1f3f9.png">
      <span class="{$this->getChangedClassForEncounterRange()}">
          {$this->fight->getCurrentEncounterRange()} ({$this->fight->getCurrentEncounterRange()->getInMeters($this->tables)} m)
      </span>
    </div>
    <div class="col-sm-3">
      Maximální dostřel
      <img alt="Luk se šípem" class="line-sized" src="/images/emojione/bow-and-arrow-1f3f9.png">
      <span class="{$this->getChangedClassForMaximalRange()}">
          {$this->fight->getCurrentRangedWeaponMaximalRange()} ({$this->fight->getCurrentRangedWeaponMaximalRange()->getInMeters($this->tables)} m)
      </span>
    </div>
  </div>
</div>
HTML;
    }

    private function getRangedTargetDistanceInputName(): string
    {
        return FightRequest::RANGED_TARGET_DISTANCE;
    }

    private function getRangedTargetSizeInputName(): string
    {
        return FightRequest::RANGED_TARGET_SIZE;
    }

    private function getChangedClassForEncounterRange()
    {
        return $this->htmlHelper->getCssClassForChangedValue(
            $this->fight->getPreviousEncounterRange(),
            $this->fight->getCurrentEncounterRange()
        );
    }

    private function getChangedClassForMaximalRange()
    {
        return $this->htmlHelper->getCssClassForChangedValue(
            $this->fight->getPreviousEncounterRange(),
            $this->fight->getCurrentEncounterRange()
        );
    }
}