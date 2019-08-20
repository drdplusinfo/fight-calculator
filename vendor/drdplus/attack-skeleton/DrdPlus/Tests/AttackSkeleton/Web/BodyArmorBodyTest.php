<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Web;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomBodyArmorBody;
use DrdPlus\AttackSkeleton\Web\BodyArmorBody;
use DrdPlus\Tests\AttackSkeleton\AbstractAttackTest;

class BodyArmorBodyTest extends AbstractAttackTest
{
    /**
     * @test
     */
    public function I_can_use_template_with_body_armors(): void
    {
        $bodyArmorBody = new BodyArmorBody(
            $this->getEmptyCustomArmamentsState(),
            $this->getEmptyCurrentArmamentValues(),
            $this->getDefaultCurrentArmaments(),
            $this->getAllPossibleArmaments(),
            $this->getEmptyArmamentsUsabilityMessages(),
            $this->getHtmlHelper(),
            Armourer::getIt(),
            new AddCustomBodyArmorBody($this->getHtmlHelper())
        );
        self::assertSame(
            <<<HTML
<div class="row " id="chooseBodyArmor">
  <div class="col">
    <div class="messages">
      
    </div>
    <a title="Přidat vlastní zbroj" href="?action=add_new_body_armor" class="btn btn-success btn-sm add">+</a>
    <label>
      <select name="body_armor" title="Zbroj">
        <option value="without_armor" selected >
  beze zbroje +0
</option>
<option value="padded_armor"  >
  prošívaná zbroj +2
</option>
<option value="leather_armor"  >
  kožená zbroj +3
</option>
<option value="hobnailed_armor"  >
  pobíjená zbroj +4
</option>
<option value="chainmail_armor"  >
  kroužková zbroj +6
</option>
<option value="scale_armor"  >
  šupinová zbroj +7
</option>
<option value="plate_armor"  >
  plátová zbroj +9
</option>
<option value="full_plate_armor"  >
  plná plátová zbroj +10
</option>
      </select>
    </label>
  </div>
</div>
HTML
            ,
            \trim($bodyArmorBody->getValue())
        );
    }
}