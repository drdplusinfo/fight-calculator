<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Web;

use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomMeleeWeaponBody;
use DrdPlus\AttackSkeleton\Web\MeleeWeaponBody;
use DrdPlus\Tests\AttackSkeleton\AbstractAttackTest;

class MeleeWeaponBodyTest extends AbstractAttackTest
{

    /**
     * @test
     */
    public function I_can_use_template_with_melee_weapons(): void
    {
        $meleeWeaponBody = new MeleeWeaponBody(
            $this->getEmptyCustomArmamentsState(),
            $this->getEmptyCurrentArmamentValues(),
            $this->getDefaultCurrentArmaments(),
            $this->getAllPossibleArmaments(),
            $this->getEmptyArmamentsUsabilityMessages(),
            $this->getHtmlHelper(),
            new AddCustomMeleeWeaponBody($this->getHtmlHelper())
        );
        self::assertSame(<<<HTML
<div class="">
    <div class="row messages">
      
    </div>
    <div class="row" id="chooseMeleeWeapon">
        <div class="col">
            <a title="Přidat vlastní zbraň na blízko" href="?action=add_new_melee_weapon" class="button add">+</a>
            <label>
                <select name="melee_weapon" title="Zbraň na blízko">
                    <optgroup label="sekery">
    <option value="light_axe"  >
  lehká sekerka
</option><option value="axe"  >
  sekera
</option><option value="war_axe"  >
  válečná sekera
</option><option value="two_handed_axe"  >
  obouruční sekera
</option>
</optgroup><optgroup label="nože a dýky">
    <option value="knife"  >
  nůž
</option><option value="dagger"  >
  dýka
</option><option value="stabbing_dagger"  >
  bodná dýka
</option><option value="long_knife"  >
  dlouhý nůž
</option><option value="long_dagger"  >
  dlouhá dýka
</option>
</optgroup><optgroup label="palice a kyje">
    <option value="cudgel"  >
  obušek
</option><option value="club"  >
  kyj
</option><option value="hobnailed_club"  >
  okovaný kyj
</option><option value="light_mace"  >
  lehký palcát
</option><option value="mace"  >
  palcát
</option><option value="heavy_club"  >
  těžký kyj
</option><option value="war_hammer"  >
  válečné kladivo
</option><option value="two_handed_club"  >
  obouruční kyj
</option><option value="heavy_sledgehammer"  >
  těžký perlík
</option>
</optgroup><optgroup label="řemdihy a bijáky">
    <option value="light_morgenstern"  >
  lehký biják
</option><option value="morgenstern"  >
  biják
</option><option value="heavy_morgenstern"  >
  těžký biják
</option><option value="flail"  >
  cep
</option><option value="morningstar"  >
  řemdih
</option><option value="hobnailed_flail"  >
  okovaný cep
</option><option value="heavy_morningstar"  >
  těžký řemdih
</option>
</optgroup><optgroup label="šavle a tesáky">
    <option value="machete"  >
  mačeta
</option><option value="light_saber"  >
  lehká šavle
</option><option value="bowie_knife"  >
  tesák
</option><option value="saber"  >
  šavle
</option><option value="heavy_saber"  >
  těžká šavle
</option>
</optgroup><optgroup label="hole a kopí">
    <option value="light_spear"  >
  lehké kopí
</option><option value="shortened_staff"  >
  zkrácená hůl
</option><option value="light_staff"  >
  lehká hůl
</option><option value="spear"  >
  kopí
</option><option value="hobnailed_staff"  >
  okovaná hůl
</option><option value="long_spear"  >
  dlouhé kopí
</option><option value="heavy_hobnailed_staff"  >
  těžká okovaná hůl
</option><option value="pike"  >
  píka
</option><option value="metal_staff"  >
  kovová hůl
</option>
</optgroup><optgroup label="meče">
    <option value="short_sword"  >
  krátký meč
</option><option value="hanger"  >
  krátký široký meč
</option><option value="glaive"  >
  široký meč
</option><option value="long_sword"  >
  dlouhý meč
</option><option value="one_and_half_handed_sword"  >
  jedenapůlruční meč
</option><option value="barbarian_sword"  >
  barbarský meč
</option><option value="two_handed_sword"  >
  obouruční meč
</option>
</optgroup><optgroup label="sudlice a trojzubce">
    <option value="pitchfork"  >
  vidle
</option><option value="light_voulge"  >
  lehká sudlice
</option><option value="light_trident"  >
  lehký trojzubec
</option><option value="halberd"  >
  halapartna
</option><option value="heavy_voulge"  >
  těžká sudlice
</option><option value="heavy_trident"  >
  těžký trojzubec
</option><option value="heavy_halberd"  >
  těžká halapartna
</option>
</optgroup><optgroup label="beze zbraně">
    <option value="hand" selected >
  ruka
</option><option value="hobnailed_glove"  >
  okovaná rukavice
</option><option value="leg"  >
  noha
</option><option value="hobnailed_boot"  >
  okovaná bota
</option>
</optgroup>
                </select>
            </label>
        </div>
        <div class="col">
            <label>
                <input type="radio" value="main_hand" name="melee_weapon_holding" checked>
                v dominantní ruce
            </label>
        </div>
        <div class="col">
            <label>
                <input type="radio" value="offhand" name="melee_weapon_holding" >
                v druhé ruce
            </label>
        </div>
        <div class="col">
            <label>
                <input type="radio" value="two_hands"
                       name="melee_weapon_holding" >
                obouručně
            </label>
        </div>
    </div>
</div>
HTML
            , \trim($meleeWeaponBody->getValue())
        );
    }

}