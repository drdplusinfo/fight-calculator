<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Web;

use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomRangedWeaponBody;
use DrdPlus\AttackSkeleton\Web\RangedWeaponBody;
use DrdPlus\Tests\AttackSkeleton\AbstractAttackTest;

class RangedWeaponBodyTest extends AbstractAttackTest
{

    /**
     * @test
     */
    public function I_can_use_template_with_ranged_weapons(): void
    {
        $rangedWeaponBody = new RangedWeaponBody(
            $this->getEmptyCustomArmamentsState(),
            $this->getEmptyCurrentArmamentValues(),
            $this->getDefaultCurrentArmaments(),
            $this->getAllPossibleArmaments(),
            $this->getEmptyArmamentsUsabilityMessages(),
            $this->getHtmlHelper(),
            new AddCustomRangedWeaponBody($this->getHtmlHelper())
        );
        self::assertSame(<<<HTML
<div class="">
    <div class="row messages">
      
    </div>
    <div class="row" id="chooseRangedWeapon">
      <div class="col">
    <a title="Přidat vlastní zbraň na dálku" href="?action=add_new_ranged_weapon" class="button add">+</a>
    <label>
        <select name="ranged_weapon" title="Zbraň na dálku">
            <optgroup label="vrhací zbraně">
    <option value="sand" selected >
  písek
</option><option value="rock"  >
  kámen
</option><option value="throwing_dagger"  >
  vrhací dýka
</option><option value="light_throwing_axe"  >
  lehká vrhací sekera
</option><option value="war_throwing_axe"  >
  válečná vrhací sekera
</option><option value="throwing_hammer"  >
  vrhací kladivo
</option><option value="shuriken"  >
  hvězdice
</option><option value="spear"  >
  kopí
</option><option value="javelin"  >
  oštěp
</option><option value="sling"  >
  prak
</option>
</optgroup><optgroup label="luky">
    <option value="short_bow"  >
  krátký luk
</option><option value="long_bow"  >
  dlouhý luk
</option><option value="short_composite_bow"  >
  krátký skládaný luk
</option><option value="long_composite_bow"  >
  dlouhý skládaný luk
</option><option value="power_bow"  >
  silový luk
</option>
</optgroup><optgroup label="kuše">
    <option value="minicrossbow"  >
  minikuše
</option><option value="light_crossbow"  >
  lehká kuše
</option><option value="military_crossbow"  >
  válečná kuše
</option><option value="heavy_crossbow"  >
  těžká kuše
</option>
</optgroup>
        </select>
    </label>
</div>
      <div class="col">
    <label>
        <input type="radio" value="main_hand" name="ranged_weapon_holding" checked>
        v dominantní ruce
    </label>
</div>
<div class="col">
    <label>
        <input type="radio" value="offhand" name="ranged_weapon_holding" >
        v druhé ruce
    </label>
</div>
<div class="col">
    <label>
        <input type="radio" value="two_hands"
               name="ranged_weapon_holding" >
        obouručně
    </label>
</div>
    </div>
</div>
HTML
            , \trim($rangedWeaponBody->getValue())
        );
    }
}