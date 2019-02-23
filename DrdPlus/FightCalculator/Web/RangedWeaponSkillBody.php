<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\Web\AbstractArmamentBody;
use DrdPlus\Codes\Skills\SkillCode;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\Fight;
use DrdPlus\FightCalculator\FightRequest;

class RangedWeaponSkillBody extends AbstractArmamentBody
{
    /**
     * @var CurrentArmamentsWithSkills
     */
    private $currentArmamentsWithSkills;
    /**
     * @var Fight
     */
    private $fight;
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;

    public function __construct(CurrentArmamentsWithSkills $currentArmamentsWithSkills, Fight $fight, HtmlHelper $htmlHelper)
    {
        $this->currentArmamentsWithSkills = $currentArmamentsWithSkills;
        $this->fight = $fight;
        $this->htmlHelper = $htmlHelper;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return <<<HTML
<div class="col">
    <label>
        <select name="{$this->getRangedFightSkillSelectName()}">
            {$this->getPossibleRangedFightSkills()}
        </select>
    </label>
    <span class="skill-ranks" data-history-skill-ranks="{$this->getHistoryRangedSkillRanks()}">
        <label>
          na stupni <input type="radio" value="0" name="{$this->getRangedFightSkillRankInputName()}" {$this->getCheckedForRangedFightSkillValue(0)}>0,
        </label>
        <label>
          <input type="radio" value="1" name="{$this->getRangedFightSkillRankInputName()}" {$this->getCheckedForRangedFightSkillValue(1)}>1,
        </label>
        <label>
          <input type="radio" value="2" name="{$this->getRangedFightSkillRankInputName()}" {$this->getCheckedForRangedFightSkillValue(2)}>2,
        </label>
        <label>
          <input type="radio" value="3" name="{$this->getRangedFightSkillRankInputName()}" {$this->getCheckedForRangedFightSkillValue(3)}>3
        </label>
    </span>
</div>
HTML;
    }

    private function getHistoryRangedSkillRanks(): string
    {
        return \htmlspecialchars($this->fight->getHistoryRangedSkillRanksJson());
    }

    private function getPossibleRangedFightSkills(): string
    {
        $possibleRangedFightSkills = [];
        foreach ($this->fight->getPossibleRangedFightSkills() as $possibleRangedFightSkill) {
            $possibleRangedFightSkills[] = <<<HTML
<option value="{$possibleRangedFightSkill->getValue()}" {$this->getSelectedForSkill($possibleRangedFightSkill)}>
    {$possibleRangedFightSkill->translateTo('cs')}
</option>
HTML;
        }
        return \implode("\n", $possibleRangedFightSkills);
    }

    private function getRangedFightSkillSelectName(): string
    {
        return FightRequest::RANGED_FIGHT_SKILL;
    }

    private function getRangedFightSkillRankInputName(): string
    {
        return FightRequest::RANGED_FIGHT_SKILL_RANK;
    }

    private function getCheckedForRangedFightSkillValue(int $matchingRank): string
    {
        return $this->htmlHelper->getChecked($this->currentArmamentsWithSkills->getCurrentRangedFightSkillRank(), $matchingRank);
    }

    private function getSelectedForSkill(SkillCode $possibleRangedFightSkill): string
    {
        return $this->htmlHelper->getSelected($possibleRangedFightSkill, $this->currentArmamentsWithSkills->getCurrentRangedFightSkillCode());
    }
}