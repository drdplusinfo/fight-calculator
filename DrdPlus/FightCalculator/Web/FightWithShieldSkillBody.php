<?php declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\Web\AbstractArmamentBody;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\FightRequest;

class FightWithShieldSkillBody extends AbstractArmamentBody
{
    /**
     * @var CurrentArmamentsWithSkills
     */
    private $currentArmamentsWithSkills;
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;

    public function __construct(CurrentArmamentsWithSkills $currentArmamentsWithSkills, HtmlHelper $htmlHelper)
    {
        $this->currentArmamentsWithSkills = $currentArmamentsWithSkills;
        $this->htmlHelper = $htmlHelper;
    }

    public function getValue(): string
    {
        return <<<HTML
<a class="keyword" target="_blank" href="https://pph.drdplus.info/?trial=1#boj_se_zbrani">{$this->getFightWithShieldHumanName()}</a>
<span class="skill-ranks">
    <label>
      na stupni <input type="radio" value="0" name="{$this->getFightWithShieldsSkillRankInputName()}" {$this->getFightWithShieldSkillValueChecked(0)}>0,
    </label>
    <label>
      <input type="radio" value="1" name="{$this->getFightWithShieldsSkillRankInputName()}" {$this->getFightWithShieldSkillValueChecked(1)}>1,
    </label>
    <label>
      <input type="radio" value="2" name="{$this->getFightWithShieldsSkillRankInputName()}" {$this->getFightWithShieldSkillValueChecked(2)}>2,
    </label>
    <label>
      <input type="radio" value="3" name="{$this->getFightWithShieldsSkillRankInputName()}" {$this->getFightWithShieldSkillValueChecked(3)}>3
    </label>
</span>
HTML;
    }

    private function getFightWithShieldHumanName(): string
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS)->translateTo('cs');
    }

    private function getFightWithShieldsSkillRankInputName(): string
    {
        return FightRequest::FIGHT_WITH_SHIELDS_SKILL_RANK;
    }

    private function getFightWithShieldSkillValueChecked(int $matchingRank): string
    {
        return $this->htmlHelper->getChecked($this->currentArmamentsWithSkills->getCurrentFightWithShieldsSkillRank(), $matchingRank);
    }
}