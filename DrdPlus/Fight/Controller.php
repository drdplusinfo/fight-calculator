<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use Granam\Strict\Object\StrictObject;

class Controller extends StrictObject
{
    /**
     * @return MeleeWeaponCode|null
     */
    public function getSelectedMeleeWeapon()
    {
        if (empty($_GET['meleeWeapon'])) {
            return null;
        }

        return MeleeWeaponCode::getIt($_GET['meleeWeapon']);
    }

    /**
     * @return RangedWeaponCode|null
     */
    public function getSelectedRangedWeapon()
    {
        if (empty($_GET['rangedWeapon'])) {
            return null;
        }

        return RangedWeaponCode::getIt($_GET['rangedWeapon']);
    }
}