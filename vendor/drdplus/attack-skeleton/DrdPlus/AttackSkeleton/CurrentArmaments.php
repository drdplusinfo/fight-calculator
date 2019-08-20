<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\ItemHoldingCode;
use Granam\Strict\Object\StrictObject;

class CurrentArmaments extends StrictObject
{
    use UsingArmaments;

    /** @var CurrentArmamentsValues */
    private $currentArmamentsValues;
    /** @var Armourer */
    private $armourer;
    /** @var CurrentProperties */
    private $currentProperties;

    public function __construct(
        CurrentProperties $currentProperties,
        CurrentArmamentsValues $currentArmamentsValues,
        Armourer $armourer,
        CustomArmamentsRegistrar $customArmamentsRegistrar
    )
    {
        $this->currentProperties = $currentProperties;
        $this->currentArmamentsValues = $currentArmamentsValues;
        $this->armourer = $armourer;
        $customArmamentsRegistrar->registerCustomArmaments($currentArmamentsValues);
    }

    /**
     * @return MeleeWeaponCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     */
    public function getCurrentMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->currentArmamentsValues->getMeleeWeaponValue();
        if (!$meleeWeaponValue) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }
        $meleeWeapon = MeleeWeaponCode::getIt($meleeWeaponValue);
        $weaponHolding = $this->getCurrentMeleeWeaponHolding($meleeWeapon);
        if (!$this->canUseWeaponlike($meleeWeapon, $weaponHolding, $this->armourer, $this->currentProperties)) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return $meleeWeapon;
    }

    /**
     * @param MeleeWeaponCode|null $currentWeapon
     * @return ItemHoldingCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     */
    public function getCurrentMeleeWeaponHolding(MeleeWeaponCode $currentWeapon = null): ItemHoldingCode
    {
        $meleeWeaponHoldingValue = $this->currentArmamentsValues->getMeleeWeaponHoldingValue();
        if ($meleeWeaponHoldingValue === null) {
            $meleeWeaponHoldingValue = ItemHoldingCode::MAIN_HAND;
        }

        return $this->getWeaponlikeHolding(
            $currentWeapon ?? $this->getCurrentMeleeWeapon(),
            $meleeWeaponHoldingValue,
            $this->armourer
        );
    }

    /**
     * @return ShieldCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getCurrentShieldForMelee(): ShieldCode
    {
        $currentShield = $this->getCurrentShield();
        if ($currentShield->isUnarmed()) {
            return $currentShield;
        }
        if ($this->getCurrentMeleeWeaponHolding()->holdsByTwoHands()
            || !$this->canUseShield(
                $currentShield,
                $this->getCurrentMeleeShieldHolding($currentShield),
                $this->armourer,
                $this->currentProperties->getCurrentStrength(),
                $this->currentProperties->getCurrentSize()
            )
        ) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return $currentShield;
    }

    /**
     * @return ShieldCode
     */
    public function getCurrentShield(): ShieldCode
    {
        $currentShieldValue = $this->currentArmamentsValues->getShieldValue() ?: ShieldCode::WITHOUT_SHIELD;
        $currentShield = ShieldCode::getIt($currentShieldValue);
        if ($currentShield->isUnarmed()) {
            return $currentShield;
        }
        if ($this->canUseShield(
            $currentShield,
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            $this->armourer,
            $this->currentProperties->getCurrentStrength(),
            $this->currentProperties->getCurrentSize()
        )) {
            return $currentShield;
        }
        return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
    }

    /**
     * @param ShieldCode|null $currentShield
     * @return ItemHoldingCode
     */
    public function getCurrentMeleeShieldHolding(ShieldCode $currentShield = null): ItemHoldingCode
    {
        return $this->getShieldHolding(
            $this->getCurrentMeleeWeaponHolding(),
            $this->getCurrentMeleeWeapon(),
            $currentShield ?? ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD),
            $this->armourer
        );
    }

    /**
     * @return RangedWeaponCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     */
    public function getCurrentRangedWeapon(): RangedWeaponCode
    {
        $rangedWeaponValue = $this->currentArmamentsValues->getRangedWeaponValue();
        if (!$rangedWeaponValue) {
            return RangedWeaponCode::getIt(RangedWeaponCode::SAND);
        }
        $rangedWeapon = RangedWeaponCode::getIt($rangedWeaponValue);
        $weaponHolding = $this->getWeaponlikeHolding(
            $rangedWeapon,
            $this->currentArmamentsValues->getRangedWeaponHoldingValue(),
            $this->armourer
        );
        if (!$this->canUseWeaponlike($rangedWeapon, $weaponHolding, $this->armourer, $this->currentProperties)) {
            return RangedWeaponCode::getIt(RangedWeaponCode::SAND);
        }

        return $rangedWeapon;
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     */
    public function getCurrentRangedWeaponHolding(): ItemHoldingCode
    {
        $rangedWeaponHoldingValue = $this->currentArmamentsValues->getRangedWeaponHoldingValue();
        if ($rangedWeaponHoldingValue === null) {
            $rangedWeaponHoldingValue = ItemHoldingCode::MAIN_HAND;
        }

        return $this->getWeaponlikeHolding($this->getCurrentRangedWeapon(), $rangedWeaponHoldingValue, $this->armourer);
    }

    /**
     * @return ShieldCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getCurrentShieldForRanged(): ShieldCode
    {
        $currentShield = $this->getCurrentShield();
        if ($currentShield->isUnarmed()) {
            return $currentShield;
        }
        if ($this->getCurrentRangedWeaponHolding()->holdsByTwoHands()
            || !$this->canUseShield(
                $currentShield,
                $this->getCurrentRangedShieldHolding($currentShield),
                $this->armourer,
                $this->currentProperties->getCurrentStrength(),
                $this->currentProperties->getCurrentSize()
            )
        ) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return $currentShield;
    }

    public function getCurrentRangedShieldHolding(ShieldCode $shield = null): ItemHoldingCode
    {
        return $this->getShieldHolding(
            $this->getCurrentRangedWeaponHolding(),
            $this->getCurrentRangedWeapon(),
            $shield ?? ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD),
            $this->armourer
        );
    }

    public function getCurrentHelm(): HelmCode
    {
        $helmValue = $this->currentArmamentsValues->getHelmValue();
        if (!$helmValue) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }
        $currentHelm = HelmCode::getIt($helmValue);
        if (!$this->canUseArmament(
            $currentHelm,
            $this->currentProperties->getCurrentStrength(),
            $this->armourer,
            $this->currentProperties->getCurrentSize()
        )) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }

        return HelmCode::getIt($helmValue);
    }

    public function getCurrentShieldForMeleeCover(): int
    {
        return $this->armourer->getCoverOfShield($this->getCurrentShieldForMelee());
    }

    public function getCurrentShieldForRangedCover(): int
    {
        return $this->armourer->getCoverOfShield($this->getCurrentShieldForRanged());
    }

    public function getCurrentHelmProtection(): int
    {
        return $this->armourer->getProtectionOfHelm($this->getCurrentHelm());
    }

    public function getCurrentBodyArmorProtection(): int
    {
        return $this->armourer->getProtectionOfBodyArmor($this->getCurrentBodyArmor());
    }

    public function getCurrentBodyArmor(): BodyArmorCode
    {
        $bodyArmorValue = $this->currentArmamentsValues->getBodyArmorValue();
        if (!$bodyArmorValue) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }
        $currentBodyArmor = BodyArmorCode::getIt($bodyArmorValue);
        if (!$this->canUseArmament(
            $currentBodyArmor,
            $this->currentProperties->getCurrentStrength(),
            $this->armourer,
            $this->currentProperties->getCurrentSize()
        )) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }

        return BodyArmorCode::getIt($bodyArmorValue);
    }

}