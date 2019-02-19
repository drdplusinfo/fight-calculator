<?php
namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\AttackRequest;
use DrdPlus\AttackSkeleton\PreviousArmaments;
use DrdPlus\AttackSkeleton\PreviousProperties;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\CalculatorSkeleton\History;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\CalculatorSkeleton\Partials\AbstractCalculatorContentTest;
use Mockery\MockInterface;

class PreviousArmamentsTest extends AbstractCalculatorContentTest
{
    /**
     * @test
     */
    public function I_will_get_hand_as_previous_melee_weapon_if_none(): void
    {
        $previousArmaments = new PreviousArmaments(
            $this->createHistory([AttackRequest::MELEE_WEAPON => null, AttackRequest::MELEE_WEAPON_HOLDING => null]),
            $this->createPreviousProperties(),
            Armourer::getIt(),
            Tables::getIt()
        );
        $previousArmaments->getPreviousMeleeWeapon();
    }

    /**
     * @param array $namesToValues
     * @return History|MockInterface
     */
    private function createHistory(array $namesToValues): History
    {
        $history = $this->mockery(History::class);
        $history->shouldReceive('getValue')
            ->andReturnUsing(function (string $name) use ($namesToValues) {
                return $namesToValues[$name];
            });
        return $history;
    }

    /**
     * @return PreviousProperties|MockInterface
     */
    private function createPreviousProperties(): PreviousProperties
    {
        $previousProperties = $this->mockery(PreviousProperties::class);
        $previousProperties->shouldReceive('getPreviousStrength')
            ->andReturn(Strength::getIt(0));
        $previousProperties->shouldReceive('getPreviousSize')
            ->andReturn(Size::getIt(0));
        return $previousProperties;
    }

    /**
     * @test
     */
    public function I_can_get_previous_melee_weapon(): void
    {
        $previousArmaments = new PreviousArmaments(
            $this->createHistory([
                AttackRequest::MELEE_WEAPON => MeleeWeaponCode::HANGER,
                AttackRequest::MELEE_WEAPON_HOLDING => null,
            ]),
            $this->createPreviousProperties(),
            $this->createArmourer(true),
            Tables::getIt()
        );
        self::assertSame(MeleeWeaponCode::getIt(MeleeWeaponCode::HANGER), $previousArmaments->getPreviousMeleeWeapon());
    }

    /**
     * @param bool $canUseArmament
     * @return Armourer|MockInterface
     */
    private function createArmourer(bool $canUseArmament): Armourer
    {
        $armourer = $this->mockery(Armourer::class);
        $armourer->shouldReceive('canUseArmament')
            ->andReturn($canUseArmament);
        $armourer->shouldReceive('isTwoHandedOnly')
            ->andReturn(true);
        $armourer->shouldReceive('getStrengthForWeaponOrShield')
            ->andReturn(Strength::getIt(0));
        return $armourer;
    }

    /**
     * @test
     */
    public function I_will_get_hand_as_previous_melee_weapon_if_can_not_use_given(): void
    {
        $previousArmaments = new PreviousArmaments(
            $this->createHistory([
                AttackRequest::MELEE_WEAPON => MeleeWeaponCode::HANGER,
                AttackRequest::MELEE_WEAPON_HOLDING => null,
            ]),
            $this->createPreviousProperties(),
            $this->createArmourer(false),
            Tables::getIt()
        );
        self::assertSame(MeleeWeaponCode::getIt(MeleeWeaponCode::HAND), $previousArmaments->getPreviousMeleeWeapon());
    }

    /**
     * @test
     */
    public function I_can_get_previous_ranged_weapon(): void
    {
        $previousArmaments = new PreviousArmaments(
            $this->createHistory([
                AttackRequest::RANGED_WEAPON => RangedWeaponCode::MINICROSSBOW,
                AttackRequest::RANGED_WEAPON_HOLDING => null,
            ]),
            $this->createPreviousProperties(),
            $this->createArmourer(true),
            Tables::getIt()
        );
        self::assertSame(RangedWeaponCode::getIt(RangedWeaponCode::MINICROSSBOW), $previousArmaments->getPreviousRangedWeapon());
    }
}
