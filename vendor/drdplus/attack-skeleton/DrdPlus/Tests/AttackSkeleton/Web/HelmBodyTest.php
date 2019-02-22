<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Web;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\Web\AddCustomArmament\AddCustomHelmBody;
use DrdPlus\AttackSkeleton\Web\HelmBody;
use DrdPlus\Tests\AttackSkeleton\AbstractAttackTest;

class HelmBodyTest extends AbstractAttackTest
{

    /**
     * @test
     */
    public function I_can_use_template_with_helms(): void
    {
        $helmBody = new HelmBody(
            $this->getEmptyCustomArmamentsState(),
            $this->getEmptyCurrentArmamentValues(),
            $this->getDefaultCurrentArmaments(),
            $this->getAllPossibleArmaments(),
            $this->getEmptyArmamentsUsabilityMessages(),
            $this->getHtmlHelper(),
            Armourer::getIt(),
            new AddCustomHelmBody($this->getHtmlHelper())
        );
        self::assertSame(
            <<<HTML
<div class="row " id="chooseHelm">
  <div class="col">
    <div class="messages">
        
    </div>
    <a title="Přidat vlastní helmu" href="?action=add_new_helm" class="btn btn-success btn-sm add">+</a>
    <label>
      <select name="helm" title="Helma">
         <option value="without_helm" selected >
  bez helmy +0
</option>
<option value="leather_cap"  >
  kožená čapka +1
</option>
<option value="chainmail_hood"  >
  kroužková kukla +2
</option>
<option value="conical_helm"  >
  konická helma +3
</option>
<option value="full_helm"  >
  plná přilba +4
</option>
<option value="barrel_helm"  >
  hrncová přilba +5
</option>
<option value="great_helm"  >
  kbelcová přilba +7
</option> 
      </select>
    </label>
  </div>
</div>
HTML
            ,
            \trim($helmBody->getValue())
        );
    }

}