<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\Web\AbstractArmamentBody;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\FightRequest;

class ShieldUsageSkillBody extends AbstractArmamentBody
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
<a class="keyword" href="https://pph.drdplus.info/?trial=1#pouzivani_stitu" target="_blank">{$this->getShieldUsageHumanName()}</a>
<span class="skill-ranks">
    <label>
      na stupni <input type="radio" value="0" name="{$this->getShieldUsageSkillRankInputName()}" {$this->getShieldUsageSkillValueChecked(0)}>0,
    </label>
    <label>
      <input type="radio" value="1" name="{$this->getShieldUsageSkillRankInputName()}" {$this->getShieldUsageSkillValueChecked(1)}>1,
    </label>
    <label>
      <input type="radio" value="2" name="{$this->getShieldUsageSkillRankInputName()}" {$this->getShieldUsageSkillValueChecked(2)}>2,
    </label>
    <label>
      <input type="radio" value="3" name="{$this->getShieldUsageSkillRankInputName()}" {$this->getShieldUsageSkillValueChecked(3)}>3
    </label>
</span>
HTML;
    }

    private function getShieldUsageHumanName(): string
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::SHIELD_USAGE)->translateTo('cs');
    }

    private function getShieldUsageSkillRankInputName(): string
    {
        return FightRequest::SHIELD_USAGE_SKILL_RANK;
    }

    private function getShieldUsageSkillValueChecked(int $matchingRank): string
    {
        return $this->htmlHelper->getChecked($this->currentArmamentsWithSkills->getCurrentShieldUsageSkillRank(), $matchingRank);
    }
}