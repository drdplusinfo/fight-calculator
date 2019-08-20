<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Web;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\AttackRequest;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomShieldBody;
use DrdPlus\AttackSkeleton\Web\ShieldBody;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\Codes\Armaments\ShieldCode;

class ShieldBodyTest extends AbstractArmamentBodyTest
{
    public function provideArmamentBodyAndExpectedContent(): array
    {
        return [
            'pavise not possible' => [
                $shieldBody = new ShieldBody(
                    $this->getEmptyCustomArmamentsState(),
                    $currentArmamentValues = new CurrentArmamentsValues(
                        new CurrentValues(
                            [AttackRequest::SHIELD => $unusableShield = ShieldCode::PAVISE],
                            $this->createEmptyMemory()
                        )
                    ),
                    $this->createCurrentArmamentsWithUnusable($currentArmamentValues, [$unusableShield]),
                    $this->createPossibleArmamentsWithUnusable([$unusableShield]),
                    $this->getEmptyArmamentsUsabilityMessages(),
                    $this->getHtmlHelper(),
                    Armourer::getIt(),
                    new AddCustomShieldBody($this->getHtmlHelper())
                ),
                <<<HTML
<div class="row " id="chooseShield">
  <div class="col">
    <div class="messages">
        
    </div>
    <a title="Přidat vlastní štít" href="?action=add_new_shield" class="btn btn-success btn-sm add">+</a>
    <label>
      <select name="shield" title="Štít">
         <option value="without_shield" selected>
  bez štítu +0
</option><option value="buckler">
  pěstní štítek +2
</option><option value="small_shield">
  malý štít +4
</option><option value="medium_shield">
  střední štít +5
</option><option value="heavy_shield">
  velký štít +6
</option><option value="pavise" disabled>
  💪 pavéza +7
</option> 
      </select>
    </label>
  </div>
</div>
HTML
                ,
            ],
            'all shields possible' => [
                $shieldBody = new ShieldBody(
                    $this->getEmptyCustomArmamentsState(),
                    $this->getEmptyCurrentArmamentValues(),
                    $this->getDefaultCurrentArmaments(),
                    $this->getAllPossibleArmaments(),
                    $this->getEmptyArmamentsUsabilityMessages(),
                    $this->getHtmlHelper(),
                    Armourer::getIt(),
                    new AddCustomShieldBody($this->getHtmlHelper())
                ),
                <<<HTML
<div class="row " id="chooseShield">
  <div class="col">
    <div class="messages">
        
    </div>
    <a title="Přidat vlastní štít" href="?action=add_new_shield" class="btn btn-success btn-sm add">+</a>
    <label>
      <select name="shield" title="Štít">
         <option value="without_shield" selected>
  bez štítu +0
</option><option value="buckler">
  pěstní štítek +2
</option><option value="small_shield">
  malý štít +4
</option><option value="medium_shield">
  střední štít +5
</option><option value="heavy_shield">
  velký štít +6
</option><option value="pavise">
  pavéza +7
</option> 
      </select>
    </label>
  </div>
</div>
HTML
                ,
            ],
        ];
    }
}