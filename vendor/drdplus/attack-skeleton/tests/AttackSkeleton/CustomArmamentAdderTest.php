<?php declare(strict_types=1);

namespace Tests\DrdPlus\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\CustomArmamentAdder;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Body\PhysicalWoundTypeCode;
use DrdPlus\Tables\Measurements\Distance\DistanceBonus;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Tables;
use Granam\TestWithMockery\TestWithMockery;

class CustomArmamentAdderTest extends TestWithMockery
{
    /**
     * @test
     * @runInSeparateProcess enabled
     */
    public function I_can_add_custom_ranged_weapon()
    {
        $tables = $this->createTables();
        $customArmamentAdder = new CustomArmamentAdder(new Armourer($tables));
        $added = $customArmamentAdder->addCustomRangedWeapon(
            'foo',
            WeaponCategoryCode::getIt(WeaponCategoryCode::BOWS),
            $requiredStrength = Strength::getIt(123),
            $offensiveness = 234,
            $distanceBonus = new DistanceBonus(345, $tables->getDistanceTable()),
            $wounds = 456,
            $woundsType = PhysicalWoundTypeCode::getIt(PhysicalWoundTypeCode::CRUSH),
            $cover = 567,
            $weight = new Weight(321, Weight::KG, $tables->getWeightTable()),
            $isTwoHandedOnly = false,
            $maximalApplicableStrength = Strength::getIt(999)
        );
        self::assertTrue($added);
        $foo = RangedWeaponCode::getIt('foo');
        $rangedWeaponTable = $tables->getRangedWeaponsTableByRangedWeaponCode($foo);

        self::assertSame($requiredStrength->getValue(), $rangedWeaponTable->getRequiredStrengthOf($foo));
        self::assertSame($offensiveness, $rangedWeaponTable->getOffensivenessOf($foo));
        self::assertSame($offensiveness, $rangedWeaponTable->getOffensivenessOf($foo));
        self::assertSame($distanceBonus->getValue(), $rangedWeaponTable->getRangeOf($foo));
        self::assertSame($wounds, $rangedWeaponTable->getWoundsOf($foo));
        self::assertSame($woundsType->getValue(), $rangedWeaponTable->getWoundsTypeOf($foo));
        self::assertSame($cover, $rangedWeaponTable->getCoverOf($foo));
        self::assertSame($weight->getValue(), $rangedWeaponTable->getWeightOf($foo));
        self::assertSame($isTwoHandedOnly, $rangedWeaponTable->getTwoHandedOnlyOf($foo));
        self::assertSame($maximalApplicableStrength->getValue(), $rangedWeaponTable->getMaximalApplicableStrengthOf($foo));
    }

    private function createTables(): Tables
    {
        return new class extends Tables
        {
            public function __construct()
            {
                parent::__construct();
            }
        };
    }
}
