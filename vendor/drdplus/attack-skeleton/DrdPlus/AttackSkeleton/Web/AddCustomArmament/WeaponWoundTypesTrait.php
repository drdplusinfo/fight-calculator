<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web\AddCustomArmament;

use DrdPlus\Codes\Body\PhysicalWoundTypeCode;

trait WeaponWoundTypesTrait
{
    private function getWeaponWoundTypes(): string
    {
        $meleeWeaponWoundTypes = '';
        foreach (PhysicalWoundTypeCode::getPossibleValues() as $woundTypeValue) {
            $woundType = PhysicalWoundTypeCode::getIt($woundTypeValue);
            $meleeWeaponWoundTypes .= <<<HTML
<option value="{$woundTypeValue}">{$woundType->translateTo('cs')}</option>
HTML;
        }

        return $meleeWeaponWoundTypes;
    }
}