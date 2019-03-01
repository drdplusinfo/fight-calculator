<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\FightRequest;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class ArmorSkillBody extends StrictObject implements BodyInterface
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

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        $armorSkillValueInputName = FightRequest::ARMOR_SKILL_VALUE;
        return <<<HTML
  <div class="row skill">
    <div class="col">
      <label>
        <span class="keyword">
          <a target="_blank" href="https://pph.drdplus.info/?trial=1#noseni_zbroje">{$this->getArmorWearingSkillHumanName()}</a>
        </span>
      </label>
      <label>
        na stupni <input type="radio" value="0" name="{$armorSkillValueInputName}" {$this->getArmorSkillValueChecked(0)}>0,
      </label>
      <label>
        <input type="radio" value="1" name="{$armorSkillValueInputName}" {$this->getArmorSkillValueChecked(1)}>1,
      </label>
      <label>
        <input type="radio" value="2" name="{$armorSkillValueInputName}" {$this->getArmorSkillValueChecked(2)}>2,
      </label>
      <label>
        <input type="radio" value="3" name="{$armorSkillValueInputName}" {$this->getArmorSkillValueChecked(3)}> 3
      </label>
    </div>
  </div>
HTML;
    }

    private function getArmorWearingSkillHumanName(): string
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::ARMOR_WEARING)->translateTo('cs');
    }

    private function getArmorSkillValueChecked(int $matchingRank): string
    {
        return $this->htmlHelper->getChecked($this->currentArmamentsWithSkills->getCurrentArmorSkillRank(), $matchingRank);
    }
}