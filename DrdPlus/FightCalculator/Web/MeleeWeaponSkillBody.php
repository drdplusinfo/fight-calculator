<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\Web\AbstractArmamentBody;
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
        <select name="{$this->getMeleeFightSkillSelectName()}">
            {$this->getPossibleMeleeFightSkills()}
        </select>
    </label>
    <span class="skill-ranks" data-history-skill-ranks="{$this->getHistoryMeleeSkillRanks()}">
        <label>
          na stupni <input type="radio" value="0" name="{$this->getMeleeFightSkillRankInputName()}" {$this->getMeleeFightSkillValueChecked(0)}>0,
        </label>
        <label>
          <input type="radio" value="1" name="{$this->getMeleeFightSkillRankInputName()}" {$this->getMeleeFightSkillValueChecked(1)}>1,
        </label>
        <label>
          <input type="radio" value="2" name="{$this->getMeleeFightSkillRankInputName()}" {$this->getMeleeFightSkillValueChecked(2)}>2,
        </label>
        <label>
          <input type="radio" value="3" name="{$this->getMeleeFightSkillRankInputName()}" {$this->getMeleeFightSkillValueChecked(3)}>3
        </label>
    </span>
</div>
HTML;
    }

    private function getHistoryMeleeSkillRanks(): string
    {
        return \htmlspecialchars($this->fight->getHistoryMeleeSkillRanksJson());
    }

    private function getPossibleMeleeFightSkills(): string
    {
        $possibleMeleeFightSkills = [];
        $currentMeleeFightSkillCode = $this->currentArmamentsWithSkills->getCurrentMeleeFightSkillCode();
        foreach ($this->fight->getPossibleMeleeFightSkills() as $possibleMeleeFightSkill) {
            $possibleMeleeFightSkills[] = <<<HTML
<option value="{$possibleMeleeFightSkill->getValue()}" {$this->getSelected($possibleMeleeFightSkill, $currentMeleeFightSkillCode)}>
    {$possibleMeleeFightSkill->translateTo('cs')}
</option>
HTML;
        }
        return \implode("\n", $possibleMeleeFightSkills);
    }

    private function getMeleeFightSkillSelectName(): string
    {
        return FightRequest::MELEE_FIGHT_SKILL;
    }

    private function getMeleeFightSkillRankInputName(): string
    {
        return FightRequest::MELEE_FIGHT_SKILL_RANK;
    }

    private function getMeleeFightSkillValueChecked(int $matchingRank): string
    {
        return $this->currentArmamentsWithSkills->getCurrentMeleeFightSkillCode() === $matchingRank
            ? 'checked'
            : '';
    }
}