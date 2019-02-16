<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentProperties;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\ItemHoldingCode;

class CurrentArmamentsTest extends AbstractAttackTest
{
    /**
     * @test
     */
    public function I_am_unarmed_by_default(): void
    {
        $currentArmamentValues = $this->getEmptyCurrentArmamentValues();
        $currentArmaments = new CurrentArmaments(
            new CurrentProperties(new CurrentValues([], $this->createEmptyMemory())),
            $currentArmamentValues,
            Armourer::getIt(),
            $this->createCustomArmamentsRegistrar($currentArmamentValues)
        );
        // melee
        $this->I_have_bare_hands_as_default_melee_weapon($currentArmaments);
        $this->I_have_main_hand_as_default_melee_weapon_holding($currentArmaments);
        $this->I_have_no_shield_as_default_shield($currentArmaments);
        $this->I_have_no_shield_as_default_shield_for_melee($currentArmaments);
        $this->I_have_offhand_as_default_melee_shield_holding($currentArmaments);
        // ranged
        $this->I_have_sand_as_default_ranged_weapon($currentArmaments);
        $this->I_have_main_hand_as_default_ranged_weapon_holding($currentArmaments);
        $this->I_have_no_shield_as_default_shield_for_ranged($currentArmaments);
        $this->I_have_offhand_as_default_ranged_shield_holding($currentArmaments);
        // armor
        $this->I_have_no_helm_as_default($currentArmaments);
        $this->I_have_no_armor_as_default($currentArmaments);
    }

    // melee

    private function I_have_bare_hands_as_default_melee_weapon(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(MeleeWeaponCode::getIt(MeleeWeaponCode::HAND), $currentArmaments->getCurrentMeleeWeapon());
    }

    private function I_have_main_hand_as_default_melee_weapon_holding(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND), $currentArmaments->getCurrentMeleeWeaponHolding());
    }

    private function I_have_no_shield_as_default_shield(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD), $currentArmaments->getCurrentShield());
    }

    private function I_have_no_shield_as_default_shield_for_melee(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD), $currentArmaments->getCurrentShieldForMelee());
        self::assertSame(0, $currentArmaments->getCurrentShieldForMeleeCover());
    }

    private function I_have_offhand_as_default_melee_shield_holding(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(ItemHoldingCode::getIt(ItemHoldingCode::OFFHAND), $currentArmaments->getCurrentMeleeShieldHolding());
    }

    // ranged

    private function I_have_sand_as_default_ranged_weapon(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(RangedWeaponCode::getIt(RangedWeaponCode::SAND), $currentArmaments->getCurrentRangedWeapon());
    }

    private function I_have_main_hand_as_default_ranged_weapon_holding(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND), $currentArmaments->getCurrentRangedWeaponHolding());
    }

    private function I_have_no_shield_as_default_shield_for_ranged(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD), $currentArmaments->getCurrentShieldForRanged());
        self::assertSame(0, $currentArmaments->getCurrentShieldForRangedCover());
    }

    private function I_have_offhand_as_default_ranged_shield_holding(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(ItemHoldingCode::getIt(ItemHoldingCode::OFFHAND), $currentArmaments->getCurrentRangedShieldHolding());
    }

    // armor

    private function I_have_no_helm_as_default(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(HelmCode::getIt(HelmCode::WITHOUT_HELM), $currentArmaments->getCurrentHelm());
        self::assertSame(0, $currentArmaments->getCurrentHelmProtection());
    }

    private function I_have_no_armor_as_default(CurrentArmaments $currentArmaments): void
    {
        self::assertSame(BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR), $currentArmaments->getCurrentBodyArmor());
        self::assertSame(0, $currentArmaments->getCurrentBodyArmorProtection());
    }
}