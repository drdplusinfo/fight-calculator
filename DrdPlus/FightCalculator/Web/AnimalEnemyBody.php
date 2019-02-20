<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\FightRequest;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class AnimalEnemyBody extends StrictObject implements BodyInterface
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

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return <<<HTML
<div class="col">
    <label>
      <input type="checkbox" value="1" name="{$this->getFightingFreeWillAnimalInputName()}" {$this->getCheckedFightingFreeWillAnimal()}>
      Bojuješ se zvířetem s vlastní vůlí
    </label>
</div>
<div class="col">
    Dovednost <a class="keyword" href="https://pph.drdplus.info/#zoologie" target="_blank">{$this->getZoologyHumanName()}</a>
    <label>
      na stupni <input type="radio" value="0" name="{$this->getZoologySkillRankInputName()}" {$this->getZoologySkillRankChecked(0)}> 0,
    </label>
    <label>
      <input type="radio" value="1" name="{$this->getZoologySkillRankInputName()}" {$this->getZoologySkillRankChecked(1)}> 1,
    </label>
    <label>
      <input type="radio" value="2" name="{$this->getZoologySkillRankInputName()}" {$this->getZoologySkillRankChecked(2)}> 2,
    </label>
    <label>
      <input type="radio" value="3" name="{$this->getZoologySkillRankInputName()}" {$this->getZoologySkillRankChecked(3)}> 3
    </label>
</div>
HTML;
    }

    private function getZoologySkillRankChecked(int $rank): string
    {
        return $this->htmlHelper->getChecked($rank, $this->currentArmamentsWithSkills->getCurrentZoologySkillRank());
    }

    private function getFightingFreeWillAnimalInputName(): string
    {
        return FightRequest::FIGHTING_FREE_WILL_ANIMAL;
    }

    private function getZoologySkillRankInputName(): string
    {
        return FightRequest::ZOOLOGY_SKILL_RANK;
    }

    private function getCheckedFightingFreeWillAnimal(): string
    {
        return $this->htmlHelper->getChecked($this->currentArmamentsWithSkills->getCurrentFightingFreeWillAnimal(), true);
    }

    private function getZoologyHumanName(): string
    {
        return PsychicalSkillCode::getIt(PsychicalSkillCode::ZOOLOGY)->translateTo('cs');
    }
}