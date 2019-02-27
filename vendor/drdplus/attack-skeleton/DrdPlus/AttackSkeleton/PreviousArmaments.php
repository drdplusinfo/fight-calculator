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
        $meleeWeapon = MeleeWeaponCode::getIt($this->getPreviousMeleeWeaponValue());
        $weaponHolding = $this->getWeaponlikeHolding(
            $meleeWeapon,
            $this->getPreviousMeleeWeaponHoldingValue(),
            $this->getArmourer()
        );
        if (!$this->couldUseWeaponlike($meleeWeapon, $weaponHolding, $this->previousProperties, $this->getArmourer())) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return $meleeWeapon;
    }

    private function getPreviousMeleeWeaponValue(): string
    {
        $meleeWeaponValue = $this->history->getValue(AttackRequest::MELEE_WEAPON);
        if ($meleeWeaponValue === null) {
            return MeleeWeaponCode::HAND;
        }
        return $meleeWeaponValue;
    }

    private function getPreviousMeleeWeaponHoldingValue(): string
    {
        $previousMeleeWeaponHoldingValue = $this->history->getValue(AttackRequest::MELEE_WEAPON_HOLDING);
        if ($previousMeleeWeaponHoldingValue !== null) {
            return $previousMeleeWeaponHoldingValue;
        }
        return ItemHoldingCode::MAIN_HAND;
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
        return $this->getWeaponlikeHolding(
            $this->getPreviousMeleeWeapon(),
            $this->getPreviousMeleeWeaponHoldingValue(),
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
        $rangedWeapon = RangedWeaponCode::getIt($this->getPreviousRangedWeaponValue());
        $weaponHolding = $this->getWeaponlikeHolding(
            $rangedWeapon,
            $this->getPreviousRangedWeaponHoldingValue(),
            $this->getArmourer()
        );
        if (!$this->couldUseWeaponlike($rangedWeapon, $weaponHolding, $this->previousProperties, $this->getArmourer())) {
            return RangedWeaponCode::getIt(RangedWeaponCode::SAND);
        }

        return $rangedWeapon;
    }

    private function getPreviousRangedWeaponValue(): string
    {
        $rangedWeaponValue = $this->history->getValue(AttackRequest::RANGED_WEAPON);
        if ($rangedWeaponValue === null) {
            return RangedWeaponCode::SAND;
        }
        return $rangedWeaponValue;
    }

    /**
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @return BodyArmorCode
     */
    public function getPreviousBodyArmor(): BodyArmorCode
    {
        $previousBodyArmor = BodyArmorCode::getIt($this->getPreviousBodyArmorValue());
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

    private function getPreviousBodyArmorValue(): string
    {
        $bodyArmorValue = $this->history->getValue(AttackRequest::BODY_ARMOR);
        if ($bodyArmorValue === null) {
            return BodyArmorCode::WITHOUT_ARMOR;
        }
        return $bodyArmorValue;
    }

    /**
     * @return HelmCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    public function getPreviousHelm(): HelmCode
    {
        $previousHelm = HelmCode::getIt($this->getPreviousHelmValue());
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

    private function getPreviousHelmValue(): string
    {
        $helmValue = $this->history->getValue(AttackRequest::HELM);
        if ($helmValue === null) {
            return HelmCode::WITHOUT_HELM;
        }
        return $helmValue;
    }

    public function getPreviousShieldForMelee(): ShieldCode
    {
        $previousShield = $this->getPreviousShield();
        if ($previousShield->isUnarmed()) {
            return $previousShield;
        }
        if ($this->getPreviousMeleeWeaponHolding()->holdsByTwoHands()
            || !$this->canUseShield(
                $previousShield,
                $this->getPreviousMeleeShieldHolding($previousShield),
                $this->armourer,
                $this->previousProperties->getPreviousStrength(),
                $this->previousProperties->getPreviousSize()
            )
        ) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }
        return $previousShield;
    }

    public function getPreviousShield(): ShieldCode
    {
        $previousShield = ShieldCode::getIt($this->getPreviousShieldValue());
        if ($this->canUseShield(
            $previousShield,
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            $this->armourer,
            $this->previousProperties->getPreviousStrength(),
            $this->previousProperties->getPreviousSize()
        )) {
            return $previousShield;
        }
        return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
    }

    public function getPreviousShieldForRanged(): ShieldCode
    {
        $previousShield = $this->getPreviousShield();
        if ($previousShield->isUnarmed()) {
            return $previousShield;
        }
        if ($this->getPreviousRangedWeaponHolding()->holdsByTwoHands()
            || !$this->canUseShield(
                $previousShield,
                $this->getPreviousRangedShieldHolding($previousShield),
                $this->armourer,
                $this->previousProperties->getPreviousStrength(),
                $this->previousProperties->getPreviousSize()
            )
        ) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return $previousShield;
    }

    private function getPreviousShieldValue(): string
    {
        $shieldValue = $this->history->getValue(AttackRequest::SHIELD);
        if ($shieldValue === null) {
            return ShieldCode::WITHOUT_SHIELD;
        }
        return $shieldValue;
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
        return $this->getWeaponlikeHolding(
            $this->getPreviousRangedWeapon(),
            $this->getPreviousRangedWeaponHoldingValue(),
            $this->getArmourer()
        );
    }

    private function getPreviousRangedWeaponHoldingValue(): string
    {
        $rangedWeaponHoldingValue = $this->history->getValue(AttackRequest::RANGED_WEAPON_HOLDING);
        if ($rangedWeaponHoldingValue === null) {
            return ItemHoldingCode::MAIN_HAND;
        }
        return $rangedWeaponHoldingValue;
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
            $shieldCode ?? $this->getPreviousShieldForMelee(),
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
            $shieldCode ?? $this->getPreviousShieldForRanged(),
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