<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Web;

use DrdPlus\AttackSkeleton\Web\BodyPropertiesBody;
use DrdPlus\Tests\AttackSkeleton\AbstractAttackTest;

class BodyPropertiesBodyTest extends AbstractAttackTest
{

    /**
     * @test
     */
    public function I_can_use_template_with_body_properties(): void
    {
        $bodyPropertiesBody = new BodyPropertiesBody($this->createMaximalCurrentProperties(40, 5, 220));
        self::assertSame(<<<HTML
<div class="row body-properties">
  <div class="col">
    <div><label for="strength">Síla</label></div>
    <div><input id="strength" type="number" name="strength" min="-40" max="40"
                value="40">
    </div>
  </div>
  <div class="col">
    <div><label for="agility">Obratnost</label></div>
    <div><input id="agility" type="number" name="agility" min="-40" max="40"
                value="40">
    </div>
  </div>
  <div class="col">
    <div><label for="knack">Zručnost</label></div>
    <div><input id="knack" type="number" name="knack" min="-40" max="40"
                value="40">
    </div>
  </div>
  <div class="col">
    <div><label for="will">Vůle</label></div>
    <div><input id="will" type="number" name="will" min="-40" max="40"
                value="40">
    </div>
  </div>
  <div class="col">
    <div><label for="intelligence">Inteligence</label></div>
    <div>
      <input id="intelligence" type="number" name="intelligence" min="-40" max="40"
             value="40">
    </div>
  </div>
  <div class="col">
    <div><label for="charisma">Charisma</label></div>
    <div>
      <input id="charisma" type="number" name="charisma" min="-40" max="40"
             value="40"></div>
  </div>
  <div class="col">
    <div><label for="height">Výška v cm</label></div>
    <div>
      <input id="height" type="number" name="height_in_cm" min="110"
             max="290"
             value="220">
    </div>
  </div>
  <div class="col">
    <div><label for="size">Velikost</label></div>
    <div><input id="size" type="number" name="size" min="-10" max="10"
                value="5">
    </div>
  </div>
</div>
HTML
            ,
            \trim($bodyPropertiesBody->getValue())
        );
    }

}