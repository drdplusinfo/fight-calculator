<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\Codes\Armaments\ArmamentCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Properties\Body\Size;

trait UsingArmaments
{

    /**
     * @param WeaponlikeCode $weaponlikeCode
     * @param ItemHoldingCode $itemHoldingCode
     * @param PreviousProperties $previousProperties
     * @param Armourer $armourer
     * @return bool
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    protected function couldUseWeaponlike(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $itemHoldingCode,
        PreviousProperties $previousProperties,
        Armourer $armourer
    ): bool
    {
        return $this->couldUseArmament(
            $weaponlikeCode,
            $armourer->getStrengthForWeaponOrShield(
                $weaponlikeCode,
                $this->getWeaponlikeHolding($weaponlikeCode, $itemHoldingCode->getValue(), $armourer),
                $previousProperties->getPreviousStrength()
            ),
            $previousProperties,
            $armourer
        );
    }

    /**
     * @param ArmamentCode $armamentCode
     * @param Strength $strengthForArmament
     * @param PreviousProperties $previousProperties
     * @param Armourer $armourer
     * @return bool
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    protected function couldUseArmament(
        ArmamentCode $armamentCode,
        Strength $strengthForArmament,
        PreviousProperties $previousProperties,
        Armourer $armourer
    ): bool
    {
        return $armourer->canUseArmament($armamentCode, $strengthForArmament, $previousProperties->getPreviousSize());
    }

    /**
     * @param WeaponlikeCode $weaponlikeCode
     * @param string $weaponHolding
     * @param Armourer $armourer
     * @return ItemHoldingCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     */
    protected function getWeaponlikeHolding(WeaponlikeCode $weaponlikeCode, string $weaponHolding, Armourer $armourer): ItemHoldingCode
    {
        if ($armourer->isTwoHandedOnly($weaponlikeCode)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }
        if ($armourer->isOneHandedOnly($weaponlikeCode)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        if (!$weaponHolding) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return ItemHoldingCode::getIt($weaponHolding);
    }

    /**
     * @param ItemHoldingCode $weaponHolding
     * @param WeaponlikeCode $weaponlikeCode
     * @param ShieldCode $shield
     * @param Armourer $armourer
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    protected function getShieldHolding(
        ItemHoldingCode $weaponHolding,
        WeaponlikeCode $weaponlikeCode,
        ShieldCode $shield,
        Armourer $armourer
    ): ItemHoldingCode
    {
        if ($weaponHolding->holdsByTwoHands()) {
            if ($armourer->canHoldItByTwoHands($shield)) {
                // because two-handed weapon has to be dropped to use shield and then both hands can be used for shield
                return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
            }

            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        if ($weaponlikeCode->isUnarmed() && $armourer->canHoldItByTwoHands($shield)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }

        return $weaponHolding->getOpposite();
    }

    protected function canUseWeaponlike(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $itemHoldingCode,
        Armourer $armourer,
        CurrentProperties $currentProperties
    ): bool
    {
        return $this->canUseArmament(
            $weaponlikeCode,
            $armourer->getStrengthForWeaponOrShield(
                $weaponlikeCode,
                $this->getWeaponlikeHolding($weaponlikeCode, $itemHoldingCode->getValue(), $this->armourer),
                $currentProperties->getCurrentStrength()
            ),
            $armourer,
            $currentProperties->getCurrentSize()
        );
    }

    protected function canUseArmament(
        ArmamentCode $armamentCode,
        Strength $strengthForArmament,
        Armourer $armourer,
        Size $size
    ): bool
    {
        return $armourer->canUseArmament($armamentCode, $strengthForArmament, $size);
    }

    protected function canUseShield(
        ShieldCode $shieldCode,
        ItemHoldingCode $shieldHolding,
        Armourer $armourer,
        Strength $strength,
        Size $size
    ): bool
    {
        return $this->canUseArmament(
            $shieldCode,
            $armourer->getStrengthForWeaponOrShield(
                $shieldCode,
                $shieldHolding,
                $strength
            ),
            $armourer,
            $size
        );
    }

}