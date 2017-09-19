<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\ArmamentCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Tables\Tables;
use Granam\Strict\Object\StrictObject;

class PreviousArmaments extends StrictObject
{
    /** @var PreviousValues */
    private $previousValues;
    /** @var PreviousProperties */
    private $previousProperties;

    public function __construct(PreviousValues $previousValues, PreviousProperties $previousProperties)
    {
        $this->previousValues = $previousValues;
        $this->previousProperties = $previousProperties;
    }

    /**
     * @return MeleeWeaponCode
     */
    public function getPreviousMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->previousValues->getValue(Controller::MELEE_WEAPON);
        if (!$meleeWeaponValue) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }
        $meleeWeapon = MeleeWeaponCode::getIt($meleeWeaponValue);
        $weaponHolding = $this->getWeaponHolding(
            $meleeWeapon,
            $this->previousValues->getValue(Controller::MELEE_WEAPON_HOLDING)
        );
        if (!$this->couldUseWeaponlike($meleeWeapon, $weaponHolding)) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return $meleeWeapon;
    }

    private function couldUseWeaponlike(WeaponlikeCode $weaponlikeCode, ItemHoldingCode $itemHoldingCode): bool
    {
        return $this->couldUseArmament(
            $weaponlikeCode,
            Tables::getIt()->getArmourer()->getStrengthForWeaponOrShield(
                $weaponlikeCode,
                $this->getWeaponHolding($weaponlikeCode, $itemHoldingCode->getValue()),
                $this->previousProperties->getPreviousStrength()
            )
        );
    }

    private function couldUseArmament(ArmamentCode $armamentCode, Strength $strengthForArmament): bool
    {
        return Tables::getIt()->getArmourer()
            ->canUseArmament(
                $armamentCode,
                $strengthForArmament,
                $this->previousProperties->getPreviousSize()
            );
    }

    private function getWeaponHolding(WeaponlikeCode $weaponlikeCode, string $weaponHolding): ItemHoldingCode
    {
        if ($this->isTwoHandedOnly($weaponlikeCode)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }
        if ($this->isOneHandedOnly($weaponlikeCode)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        if (!$weaponHolding) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return ItemHoldingCode::getIt($weaponHolding);
    }

    public function isOneHandedOnly(WeaponlikeCode $weaponlikeCode): bool
    {
        return Tables::getIt()->getArmourer()->isOneHandedOnly($weaponlikeCode);
    }

    private function isTwoHandedOnly(WeaponlikeCode $weaponlikeCode): bool
    {
        return Tables::getIt()->getArmourer()->isTwoHandedOnly($weaponlikeCode);
    }
}