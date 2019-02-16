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
        $basicFightPropertyOrder = 1;
        $rows = [];
        foreach ($basicFightProperties as [$name, $value, $class, $note]) {
            $row = '';
            if ($basicFightPropertyOrder > 1 && $basicFightPropertyOrder % 2 === 1) {
                $row = '<div class="row">';
            }
            $row .= <<<HTML
<div class="col-sm-6">
  <div class="row">
    <div class="col-sm-3">{$name}</div>
    <div class="col-xs-3">
      <strong class="{$class}">{$this->htmlHelper->formatInteger($value)}</strong>
      <span class="note">{$note}</span>
    </div>
  </div>
</div>
HTML;
            if ($basicFightPropertyOrder % 2 === 0) {
                $row .= '</div>';
            }
            $rows[] = $row;
            $basicFightPropertyOrder++;
        }
        return \implode("\n", $rows);
    }

    private function getBasicFightProperties(): array
    {
        $genericFightProperties = $this->fight->getGenericFightProperties();
        $previousGenericFightProperties = $this->fight->getPreviousGenericFightProperties();
        $basicFightProperties = [];
        $basicFightProperties[] = [
            'Boj',
            $genericFightProperties->getFight(),
            $this->htmlHelper->getCssClassForChangedValue($previousGenericFightProperties->getFight(), $genericFightProperties->getFight()),
            '(není ovlivněn výzbrojí)',
        ];
        $basicFightProperties[] = [
            'Útok',
            $genericFightProperties->getAttack(),
            $this->htmlHelper->getCssClassForChangedValue($previousGenericFightProperties->getAttack(), $genericFightProperties->getAttack()),
            '(není ovlivněn výzbrojí)',
        ];
        $basicFightProperties[] = [
            'Obrana',
            $genericFightProperties->getDefense(),
            $this->htmlHelper->getCssClassForChangedValue($previousGenericFightProperties->getDefense(), $genericFightProperties->getDefense()),
            '(není ovlivněna výzbrojí)',
        ];
        $basicFightProperties[] = [
            'Střelba',
            $genericFightProperties->getShooting(),
            $this->htmlHelper->getCssClassForChangedValue($previousGenericFightProperties->getShooting(), $genericFightProperties->getShooting()),
            '(není ovlivněna výzbrojí)',
        ];
        $basicFightProperties[] = [
            'OČ <img alt="OČ" class="line-sized" src="/images/emojione/defense-number-1f6e1.png">',
            $genericFightProperties->getDefenseNumber(),
            $this->htmlHelper->getCssClassForChangedValue($previousGenericFightProperties->getDefenseNumber(), $genericFightProperties->getDefenseNumber()),
            '(ovlivněno pouze akcí, oslněním a Převahou)',
        ];
        $targetDistance = new Distance(1, Distance::METER, Tables::getIt()->getDistanceTable());
        $attackNumber = $genericFightProperties->getAttackNumber($targetDistance, Size::getIt(1));
        $basicFightProperties[] = [
            'ÚČ <img alt="ÚČ" class="line-sized" src="/images/emojione/fight-number-1f624.png">',
            $attackNumber,
            $this->htmlHelper->getCssClassForChangedValue($previousGenericFightProperties->getAttackNumber($targetDistance, Size::getIt(1)), $attackNumber),
            '',
        ];
        $basicFightProperties[] = [
            'Zbroj <img alt="Zbroj" class="line-sized" src="/images/armor-icon.png">',
            $this->currentArmaments->getCurrentBodyArmorProtection(),
            $this->htmlHelper->getCssClassForChangedValue(
                $this->previousArmaments->getProtectionOfPreviousBodyArmor(),
                $this->currentArmaments->getCurrentBodyArmorProtection()
            ),
            '',
        ];
        $basicFightProperties[] = [
            'Helma <img alt="Helma" class="line-sized" src="/images/helm-icon.png">',
            $this->currentArmaments->getCurrentHelmProtection(),
            $this->htmlHelper->getCssClassForChangedValue(
                $this->previousArmaments->getPreviousHelmProtection(),
                $this->currentArmaments->getCurrentHelmProtection()
            ),
            '',
        ];
        return $basicFightProperties;
    }
}