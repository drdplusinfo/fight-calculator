<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\Web\AbstractArmamentBody;
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

    public function __construct(CurrentArmamentsWithSkills $currentArmamentsWithSkills, Fight $fight)
    {
        $this->currentArmamentsWithSkills = $currentArmamentsWithSkills;
        $this->fight = $fight;
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
          na stupni <input type="radio" value="0" name="{$this->getRangedFightSkillRankInputName()}" {$this->getRangedFightSkillValueChecked(0)}>0,
        </label>
        <label>
          <input type="radio" value="1" name="{$this->getRangedFightSkillRankInputName()}" {$this->getRangedFightSkillValueChecked(1)}>1,
        </label>
        <label>
          <input type="radio" value="2" name="{$this->getRangedFightSkillRankInputName()}" {$this->getRangedFightSkillValueChecked(2)}>2,
        </label>
        <label>
          <input type="radio" value="3" name="{$this->getRangedFightSkillRankInputName()}" {$this->getRangedFightSkillValueChecked(3)}>3
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
        $currentRangedFightSkillCode = $this->currentArmamentsWithSkills->getCurrentRangedFightSkillCode();
        foreach ($this->fight->getPossibleRangedFightSkills() as $possibleRangedFightSkill) {
            $possibleRangedFightSkills[] = <<<HTML
<option value="{$possibleRangedFightSkill->getValue()}" {$this->getSelected($possibleRangedFightSkill, $currentRangedFightSkillCode)}>
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

    private function getRangedFightSkillValueChecked(int $matchingRank): string
    {
        return $this->currentArmamentsWithSkills->getCurrentRangedFightSkillCode() === $matchingRank
            ? 'checked'
            : '';
    }
}