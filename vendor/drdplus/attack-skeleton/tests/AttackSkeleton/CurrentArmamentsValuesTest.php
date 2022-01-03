<?php declare(strict_types=1);

namespace Tests\DrdPlus\AttackSkeleton;

use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use Granam\TestWithMockery\TestWithMockery;
use Mockery\MockInterface;

class CurrentArmamentsValuesTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_get_current_custom_helm_values()
    {
        $helmValues = [
            CurrentArmamentsValues::CUSTOM_HELM_NAME => 'foo',
            CurrentArmamentsValues::CUSTOM_HELM_REQUIRED_STRENGTH => 123,
            CurrentArmamentsValues::CUSTOM_HELM_RESTRICTION => 111,
            CurrentArmamentsValues::CUSTOM_HELM_PROTECTION => 222,
            CurrentArmamentsValues::CUSTOM_HELM_WEIGHT => 333,
        ];
        $currentArmamentsValues = new CurrentArmamentsValues($this->createCurrentValues($helmValues));
        self::assertSame(
            ['foo' => $helmValues],
            $currentArmamentsValues->getCurrentCustomHelmsValues()
        );
    }

    /**
     * @param array $plainValues
     * @return CurrentValues|MockInterface
     */
    private function createCurrentValues(array $plainValues): CurrentValues
    {
        $currentValues = $this->mockery(CurrentValues::class);
        $currentValues->shouldReceive('getCurrentValue')
            ->andReturnUsing(fn(string $valueName) => $plainValues[$valueName] ?? null);
        return $currentValues;
    }

    /**
     * @test
     */
    public function I_can_get_current_custom_body_armor_values()
    {
        $bodyArmorValues = [
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_NAME => 'bar',
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_REQUIRED_STRENGTH => 123,
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_RESTRICTION => 111,
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_PROTECTION => 222,
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_WEIGHT => 333,
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_ROUNDS_TO_PUT_ON => 444,
        ];
        $currentArmamentsValues = new CurrentArmamentsValues($this->createCurrentValues($bodyArmorValues));
        self::assertSame(
            ['bar' => $bodyArmorValues],
            $currentArmamentsValues->getCurrentCustomBodyArmorsValues()
        );
    }

    /**
     * @test
     */
    public function I_can_get_current_custom_ranged_weapons_values()
    {
        $rangedWeaponsValues = [
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_NAME => 'baz',
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_CATEGORY => 'secret',
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_OFFENSIVENESS => 333,
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_RANGE_IN_M => 222,
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_REQUIRED_STRENGTH => 111,
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_WOUND_TYPE => 'splash',
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_WOUNDS => 444,
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_COVER => 555,
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_WEIGHT => 666,
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_TWO_HANDED_ONLY => false,
            CurrentArmamentsValues::CUSTOM_RANGED_WEAPON_MAXIMAL_APPLICABLE_STRENGTH => 777,
        ];
        $currentArmamentsValues = new CurrentArmamentsValues($this->createCurrentValues($rangedWeaponsValues));
        self::assertSame(
            ['baz' => $rangedWeaponsValues],
            $currentArmamentsValues->getCurrentCustomRangedWeaponsValues()
        );
    }
}
