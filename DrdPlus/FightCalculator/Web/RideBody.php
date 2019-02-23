<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\Web\AbstractArmamentBody;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\FightRequest;

class RideBody extends AbstractArmamentBody
{
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;
    /**
     * @var CurrentArmamentsWithSkills
     */
    private $currentArmamentsWithSkills;

    public function __construct(CurrentArmamentsWithSkills $currentArmamentsWithSkills, HtmlHelper $htmlHelper)
    {
        $this->htmlHelper = $htmlHelper;
        $this->currentArmamentsWithSkills = $currentArmamentsWithSkills;
    }

    public function getValue(): string
    {
        return <<<HTML
<div class="col">
    <label>
      <input type="checkbox" value="1" name="{$this->getOnHorsebackInputName()}" {$this->getCheckedOnHorseback()}>
      Bojuješ ze sedla
    </label>
</div>
<div class="col">
Dovednost <span class="keyword">
    <a href="https://pph.drdplus.info/#jezdectvi" target="_blank">{$this->getRidingHumanName()}</a>
</span>
<label>
na stupni <input type="radio" value="0" name="{$this->getRidingSkillRankInputName()}" {$this->getCheckedRidingSkillRank(0)}>
  0,
</label>
<label>
  <input type="radio" value="1" name="{$this->getRidingSkillRankInputName()}" {$this->getCheckedRidingSkillRank(1)}> 1,
</label>
<label>
  <input type="radio" value="2" name="{$this->getRidingSkillRankInputName()}" {$this->getCheckedRidingSkillRank(2)}> 2,
</label>
<label>
  <input type="radio" value="3" name="{$this->getRidingSkillRankInputName()}" {$this->getCheckedRidingSkillRank(3)}> 3
</label>
</div>
HTML;
    }

    private function getOnHorsebackInputName(): string
    {
        return FightRequest::ON_HORSEBACK;
    }

    private function getCheckedOnHorseback(): string
    {
        return $this->htmlHelper->getChecked($this->currentArmamentsWithSkills->getCurrentOnHorseback(), true);
    }

    private function getRidingHumanName(): string
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::RIDING)->translateTo('cs');
    }

    private function getRidingSkillRankInputName(): string
    {
        return FightRequest::RIDING_SKILL_RANK;
    }

    private function getCheckedRidingSkillRank(int $rank): string
    {
        return $this->htmlHelper->getChecked($this->currentArmamentsWithSkills->getCurrentRidingSkillRank(), $rank);
    }
}