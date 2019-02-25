<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PreviousArmaments;
use DrdPlus\FightCalculator\Fight;
use DrdPlus\FightCalculator\PreviousArmamentsWithSkills;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;

class BasicFightPropertiesBody extends StrictObject implements BodyInterface
{
    /** @var Fight */
    private $fight;
    /** @var HtmlHelper */
    private $htmlHelper;
    /** @var CurrentArmaments */
    private $currentArmaments;
    /** @var PreviousArmamentsWithSkills */
    private $previousArmaments;

    public function __construct(
        Fight $fight,
        CurrentArmaments $currentArmaments,
        PreviousArmaments $previousArmaments,
        HtmlHelper $htmlHelper
    )
    {
        $this->fight = $fight;
        $this->htmlHelper = $htmlHelper;
        $this->currentArmaments = $currentArmaments;
        $this->previousArmaments = $previousArmaments;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        $basicFightProperties = $this->getBasicFightProperties();
        $rows = [];
        foreach ($basicFightProperties as [$name, $previousValue, $currentValue, $note]) {
            $row = '';
            $row .= <<<HTML
<div class="col">
  <div>
      {$name}&nbsp;<strong class="{$this->htmlHelper->getCssClassForChangedValue($previousValue, $currentValue)}">{$this->htmlHelper->formatInteger($currentValue)}</strong>
  </div>
  <div class="note">{$note}</div>
</div>
HTML;
            $rows[] = $row;
        }
        return '<div class="row">' . \implode("\n", $rows) . '</div>';
    }

    private function getBasicFightProperties(): array
    {
        $previousGenericFightProperties = $this->fight->getPreviousGenericFightProperties();
        $currentGenericFightProperties = $this->fight->getCurrentGenericFightProperties();
        $basicFightProperties = [];
        $basicFightProperties[] = [
            'Boj',
            $previousGenericFightProperties->getFight(),
            $currentGenericFightProperties->getFight(),
            '(není ovlivněn výzbrojí)',
        ];
        $basicFightProperties[] = [
            'Útok',
            $previousGenericFightProperties->getAttack(),
            $currentGenericFightProperties->getAttack(),
            '(není ovlivněn výzbrojí)',
        ];
        $basicFightProperties[] = [
            'Obrana',
            $previousGenericFightProperties->getDefense(),
            $currentGenericFightProperties->getDefense(),
            '(není ovlivněna výzbrojí)',
        ];
        $basicFightProperties[] = [
            'Střelba',
            $previousGenericFightProperties->getShooting(),
            $currentGenericFightProperties->getShooting(),
            '(není ovlivněna výzbrojí)',
        ];
        $basicFightProperties[] = [
            'OČ',
            $previousGenericFightProperties->getDefenseNumber(),
            $currentGenericFightProperties->getDefenseNumber(),
            '(ovlivněno pouze akcí, oslněním a Převahou)',
        ];
        $targetDistance = new Distance(1, Distance::METER, Tables::getIt()->getDistanceTable());
        $basicFightProperties[] = [
            'ÚČ',
            $previousGenericFightProperties->getAttackNumber($targetDistance, Size::getIt(1)),
            $currentGenericFightProperties->getAttackNumber($targetDistance, Size::getIt(1)),
            '',
        ];
        $basicFightProperties[] = [
            'Zbroj',
            $this->previousArmaments->getProtectionOfPreviousBodyArmor(),
            $this->currentArmaments->getCurrentBodyArmorProtection(),
            '',
        ];
        $basicFightProperties[] = [
            'Helma',
            $this->previousArmaments->getPreviousHelmProtection(),
            $this->currentArmaments->getCurrentHelmProtection(),
            '',
        ];
        return $basicFightProperties;
    }
}