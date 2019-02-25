<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\Web\AbstractArmamentBody;
use DrdPlus\Codes\Skills\SkillCode;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\Fight;
use DrdPlus\FightCalculator\FightRequest;

class MeleeWeaponSkillBody extends AbstractArmamentBody
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

    public function __construct(
        CurrentArmamentsWithSkills $currentArmamentsWithSkills,
        Fight $fight,
        HtmlHelper $htmlHelper
    )
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
<div class="row">
<div class="col">
    <label>
        <select name="{$this->getMeleeFightSkillInputName()}">
            {$this->getPossibleMeleeFightSkills()}
        </select>
    </label>
    <span class="skill-ranks" data-history-skill-ranks="{$this->getHistoryMeleeSkillRanks()}">
        <label>
          na stupni <input type="radio" value="0" name="{$this->getMeleeFightSkillRankInputName()}" {$this->getCheckedForMeleeFightSkillRank(0)}>0,
        </label>
        <label>
          <input type="radio" value="1" name="{$this->getMeleeFightSkillRankInputName()}" {$this->getCheckedForMeleeFightSkillRank(1)}>1,
        </label>
        <label>
          <input type="radio" value="2" name="{$this->getMeleeFightSkillRankInputName()}" {$this->getCheckedForMeleeFightSkillRank(2)}>2,
        </label>
        <label>
          <input type="radio" value="3" name="{$this->getMeleeFightSkillRankInputName()}" {$this->getCheckedForMeleeFightSkillRank(3)}>3
        </label>
    </span>
</div>
</div>
HTML;
    }

    private function getHistoryMeleeSkillRanks(): string
    {
        return \htmlspecialchars($this->fight->getPreviousMeleeSkillRanksJson());
    }

    private function getPossibleMeleeFightSkills(): string
    {
        $possibleMeleeFightSkills = [];
        foreach ($this->fight->getPossibleMeleeFightSkills() as $possibleMeleeFightSkill) {
            $possibleMeleeFightSkills[] = <<<HTML
<option value="{$possibleMeleeFightSkill->getValue()}" {$this->getSelectedForSkill($possibleMeleeFightSkill)}>
    {$possibleMeleeFightSkill->translateTo('cs')}
</option>
HTML;
        }
        return \implode("\n", $possibleMeleeFightSkills);
    }

    private function getMeleeFightSkillInputName(): string
    {
        return FightRequest::MELEE_FIGHT_SKILL;
    }

    private function getMeleeFightSkillRankInputName(): string
    {
        return FightRequest::MELEE_FIGHT_SKILL_RANK;
    }

    private function getCheckedForMeleeFightSkillRank(int $matchingRank): string
    {
        return $this->htmlHelper->getChecked($this->currentArmamentsWithSkills->getCurrentMeleeFightSkillRank(), $matchingRank);
    }

    private function getSelectedForSkill(SkillCode $possibleMeleeFightSkill): string
    {
        return $this->htmlHelper->getSelected($possibleMeleeFightSkill, $this->currentArmamentsWithSkills->getCurrentMeleeFightSkillCode());
    }
}