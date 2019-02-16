<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\CalculatorSkeleton\History;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Tables\Tables;
use Granam\Strict\Object\StrictObject;

class PreviousArmaments extends StrictObject
{
    use UsingArmaments;

    /** @var History */
    private $history;
    /** @var PreviousProperties */
    private $previousProperties;
    /** @var Armourer */
    private $armourer;
    /** @var Tables */
    private $tables;

    public function __construct(History $history, PreviousProperties $previousProperties, Armourer $armourer, Tables $tables)
    {
        $this->history = $history;
        $this->previousProperties = $previousProperties;
        $this->armourer = $armourer;
        $this->tables = $tables;
    }

    /**
     * @return History
     */
    protected function getHistory(): History
    {
        return $this->history;
    }

    /**
     * @return PreviousProperties
     */
    protected function getPreviousProperties(): PreviousProperties
    {
        return $this->previousProperties;
    }

    protected function getTables(): Tables
    {
        return $this->tables;
    }

    /**
     * @return Armourer
     */
    protected function getArmourer(): Armourer
    {
        return $this->armourer;
    }

    /**
     * @return MeleeWeaponCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     */
    public function getPreviousMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->history->getValue(HtmlHelper::MELEE_WEAPON);
        if (!$meleeWeaponValue) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }
        $meleeWeapon = MeleeWeaponCode::getIt($meleeWeaponValue);
        $weaponHolding = $this->getWeaponlikeHolding(
            $meleeWeapon,
            $this->history->getValue(HtmlHelper::MELEE_WEAPON_HOLDING),
            $this->getArmourer()
        );
        if (!$this->couldUseWeaponlike($meleeWeapon, $weaponHolding, $this->previousProperties, $this->getArmourer())) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return $meleeWeapon;
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     */
    public function getPreviousMeleeWeaponHolding(): ItemHoldingCode
    {
        $previousMeleeWeaponHoldingValue = $this->history->getValue(HtmlHelper::MELEE_WEAPON_HOLDING);
        if ($previousMeleeWeaponHoldingValue === null) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return $this->getWeaponlikeHolding(
            $this->getPreviousMeleeWeapon(),
            $previousMeleeWeaponHoldingValue,
            $this->getArmourer()
        );
    }

    /**
     * @return RangedWeaponCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     */
    public function getPreviousRangedWeapon(): RangedWeaponCode
    {
        $rangedWeaponValue = $this->history->getValue(HtmlHelper::RANGED_WEAPON);
        if (!$rangedWeaponValue) {
            return RangedWeaponCode::getIt(RangedWeaponCode::SAND);
        }
        $rangedWeapon = RangedWeaponCode::getIt($rangedWeaponValue);
        $weaponHolding = $this->getWeaponlikeHolding(
            $rangedWeapon,
            $this->history->getValue(HtmlHelper::RANGED_WEAPON_HOLDING),
            $this->getArmourer()
        );
        if (!$this->couldUseWeaponlike($rangedWeapon, $weaponHolding, $this->previousProperties, $this->getArmourer())) {
            return RangedWeaponCode::getIt(RangedWeaponCode::SAND);
        }

        return $rangedWeapon;
    }

    /**
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @return BodyArmorCode
     */
    public function getPreviousBodyArmor(): BodyArmorCode
    {
        $previousBodyArmorValue = $this->history->getValue(HtmlHelper::BODY_ARMOR);
        if (!$previousBodyArmorValue) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }
        $previousBodyArmor = BodyArmorCode::getIt($previousBodyArmorValue);
        if (!$this->couldUseArmament(
            $previousBodyArmor,
            $this->previousProperties->getPreviousStrength(),
            $this->previousProperties,
            $this->getArmourer()
        )) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }

        return $previousBodyArmor;
    }

    /**
     * @return HelmCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    public function getPreviousHelm(): HelmCode
    {
        $previousHelmValue = $this->history->getValue(HtmlHelper::HELM);
        if (!$previousHelmValue) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }
        $previousHelm = HelmCode::getIt($previousHelmValue);
        if (!$this->couldUseArmament(
            $previousHelm,
            $this->previousProperties->getPreviousStrength(),
            $this->previousProperties,
            $this->getArmourer()
        )) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }

        return $previousHelm;
    }

    /**
     * @return ShieldCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getPreviousShield(): ShieldCode
    {
        $previousShieldValue = $this->history->getValue(HtmlHelper::SHIELD);
        if (!$previousShieldValue) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }
        $previousShield = ShieldCode::getIt($previousShieldValue);
        if ($this->getPreviousMeleeWeaponHolding()->holdsByTwoHands()
            || $this->getPreviousRangedWeaponHolding()->holdsByTwoHands()
            || !$this->couldUseShield($previousShield, $this->getPreviousMeleeShieldHolding($previousShield))
            || !$this->couldUseShield($previousShield, $this->getPreviousRangedShieldHolding($previousShield))
        ) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return $previousShield;
    }

    public function couldUseShield(ShieldCode $shieldCode, ItemHoldingCode $itemHoldingCode): bool
    {
        return $this->couldUseArmament(
            $shieldCode,
            $this->getArmourer()->getStrengthForWeaponOrShield(
                $shieldCode,
                $itemHoldingCode,
                $this->previousProperties->getPreviousStrength()
            ),
            $this->previousProperties,
            $this->getArmourer()
        );
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     */
    public function getPreviousRangedWeaponHolding(): ItemHoldingCode
    {
        $rangedWeaponHoldingValue = $this->history->getValue(HtmlHelper::RANGED_WEAPON_HOLDING);
        if ($rangedWeaponHoldingValue === null) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return $this->getWeaponlikeHolding($this->getPreviousRangedWeapon(), $rangedWeaponHoldingValue, $this->getArmourer());
    }

    /**
     * @param ShieldCode|null $shieldCode
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     */
    public function getPreviousMeleeShieldHolding(ShieldCode $shieldCode = null): ItemHoldingCode
    {
        return $this->getShieldHolding(
            $this->getPreviousMeleeWeaponHolding(),
            $this->getPreviousMeleeWeapon(),
            $shieldCode ?? $this->getPreviousShield(),
            $this->getArmourer()
        );
    }

    /**
     * @param ShieldCode|null $shieldCode
     * @return ItemHoldingCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     */
    public function getPreviousRangedShieldHolding(ShieldCode $shieldCode = null): ItemHoldingCode
    {
        return $this->getShieldHolding(
            $this->getPreviousRangedWeaponHolding(),
            $this->getPreviousRangedWeapon(),
            $shieldCode ?? $this->getPreviousShield(),
            $this->getArmourer()
        );
    }

    /**
     * @return int
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmor
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    public function getPreviousHelmProtection(): int
    {
        return $this->getArmourer()->getProtectionOfHelm($this->getPreviousHelm());
    }

    /**
     * @return int
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmor
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    public function getProtectionOfPreviousBodyArmor(): int
    {
        return $this->getArmourer()->getProtectionOfBodyArmor($this->getPreviousBodyArmor());
    }

}