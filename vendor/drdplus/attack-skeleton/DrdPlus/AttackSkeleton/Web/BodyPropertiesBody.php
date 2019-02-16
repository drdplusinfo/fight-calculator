<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web;

use DrdPlus\AttackSkeleton\CurrentProperties;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class BodyPropertiesBody extends StrictObject implements BodyInterface
{
    /** @var CurrentProperties */
    private $currentProperties;

    public function __construct(CurrentProperties $currentProperties)
    {
        $this->currentProperties = $currentProperties;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return <<<HTML
<div class="row body-properties">
  <div class="col">
    <div><label for="strength">Síla</label></div>
    <div><input id="strength" type="number" name="{$this->currentProperties->getCurrentStrength()->getCode()->getValue()}" min="-40" max="40"
                value="{$this->currentProperties->getCurrentStrength()->getValue()}">
    </div>
  </div>
  <div class="col">
    <div><label for="agility">Obratnost</label></div>
    <div><input id="agility" type="number" name="{$this->currentProperties->getCurrentAgility()->getCode()->getValue()}" min="-40" max="40"
                value="{$this->currentProperties->getCurrentAgility()->getValue()}">
    </div>
  </div>
  <div class="col">
    <div><label for="knack">Zručnost</label></div>
    <div><input id="knack" type="number" name="{$this->currentProperties->getCurrentKnack()->getCode()->getValue()}" min="-40" max="40"
                value="{$this->currentProperties->getCurrentKnack()->getValue()}">
    </div>
  </div>
  <div class="col">
    <div><label for="will">Vůle</label></div>
    <div><input id="will" type="number" name="{$this->currentProperties->getCurrentWill()->getCode()->getValue()}" min="-40" max="40"
                value="{$this->currentProperties->getCurrentWill()->getValue()}">
    </div>
  </div>
  <div class="col">
    <div><label for="intelligence">Inteligence</label></div>
    <div>
      <input id="intelligence" type="number" name="{$this->currentProperties->getCurrentIntelligence()->getCode()->getValue()}" min="-40" max="40"
             value="{$this->currentProperties->getCurrentIntelligence()->getValue()}">
    </div>
  </div>
  <div class="col">
    <div><label for="charisma">Charisma</label></div>
    <div>
      <input id="charisma" type="number" name="{$this->currentProperties->getCurrentCharisma()->getCode()->getValue()}" min="-40" max="40"
             value="{$this->currentProperties->getCurrentCharisma()->getValue()}"></div>
  </div>
  <div class="col">
    <div><label for="height">Výška v cm</label></div>
    <div>
      <input id="height" type="number" name="{$this->currentProperties->getCurrentHeightInCm()->getCode()->getValue()}" min="110"
             max="290"
             value="{$this->currentProperties->getCurrentHeightInCm()->getValue()}">
    </div>
  </div>
  <div class="col">
    <div><label for="size">Velikost</label></div>
    <div><input id="size" type="number" name="{$this->currentProperties->getCurrentSize()->getCode()->getValue()}" min="-10" max="10"
                value="{$this->currentProperties->getCurrentSize()->getValue()}">
    </div>
  </div>
</div>
HTML;
    }

}