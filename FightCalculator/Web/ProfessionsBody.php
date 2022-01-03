<?php declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\FightRequest;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class ProfessionsBody extends StrictObject implements BodyInterface
{
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;
    /**
     * @var CurrentArmamentsWithSkills
     */
    private $currentArmamentsWithSkills;

    public function __construct(
        CurrentArmamentsWithSkills $currentArmamentsWithSkills,
        HtmlHelper $htmlHelper
    )
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
<label>
Povolání
<select id="profession" name="{$this->getProfessionInputName()}">
  {$this->getProfessionOptions()}
</select>
</label>
HTML;
    }

    private function getProfessionInputName(): string
    {
        return FightRequest::PROFESSION;
    }

    private function getProfessionOptions(): string
    {
        $professionOptions = [];
        foreach (ProfessionCode::getPossibleValues() as $professionValue) {
            $professionOptions[] = <<<HTML
<option value="{$professionValue}" {$this->getProfessionSelected($professionValue)}>
  {$this->getProfessionHumanName($professionValue)}
</option>
HTML;
        }
        return \implode("\n", $professionOptions);
    }

    private function getProfessionSelected(string $professionValue): string
    {
        return $this->htmlHelper->getSelected(
            $this->currentArmamentsWithSkills->getCurrentProfessionCode()->getValue(),
            $professionValue
        );
    }

    private function getProfessionHumanName(string $professionValue): string
    {
        return ProfessionCode::getIt($professionValue)->translateTo('cs');
    }

}