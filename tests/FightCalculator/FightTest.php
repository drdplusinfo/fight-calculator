<?php declare(strict_types=1);

namespace Tests\DrdPlus\FightCalculator;

use DrdPlus\Armourer\Armourer;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\CalculatorSkeleton\History;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\CurrentProperties;
use DrdPlus\FightCalculator\Fight;
use DrdPlus\FightCalculator\FightRequest;
use DrdPlus\FightCalculator\PreviousArmamentsWithSkills;
use DrdPlus\FightCalculator\PreviousProperties;
use DrdPlus\Properties\Combat\MaximalRange;
use DrdPlus\Properties\Derived\Speed;
use DrdPlus\Tables\Combat\Attacks\AttackNumberByContinuousDistanceTable;
use DrdPlus\Tables\Tables;
use Granam\TestWithMockery\TestWithMockery;
use Mockery\MockInterface;

class FightTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideRangeAndDistance
     * @param int $maximalRangeInMeters
     * @param int $currentRangedTargetDistance
     * @param float $expectedTargetDistance
     */
    public function I_will_get_lesser_of_current_target_distance_and_weapon_maximal_range(
        int $maximalRangeInMeters,
        ?int $currentRangedTargetDistance,
        float $expectedTargetDistance
    ): void
    {
        $fight = new Fight(
            $this->createCurrentArmamentsWithSkills(RangedWeaponCode::getIt(RangedWeaponCode::POWER_BOW)),
            $this->createCurrentProperties(),
            $this->createCurrentValues([FightRequest::RANGED_TARGET_DISTANCE => $currentRangedTargetDistance]),
            $this->createPreviousArmamentsWithSkills(),
            $this->createPreviousProperties(),
            $this->createHistory(),
            $this->createArmourer($maximalRangeInMeters),
            Tables::getIt()
        );
        self::assertSame($expectedTargetDistance, $fight->getCurrentTargetDistance()->getMeters());
    }

    public function provideRangeAndDistance(): array
    {
        return [
            'negative maximal range' => [-99, null, -99.0],
            'zero maximal range' => [0, null, 0.0],
            'enormous maximal range' => [
                999999999999,
                null,
                (float)AttackNumberByContinuousDistanceTable::DISTANCE_WITH_NO_IMPACT_TO_ATTACK_NUMBER,
            ],
            'lesser target distance than maximal range' => [5, 4, 4.0],
        ];
    }

    /**
     * @return History|MockInterface
     */
    private function createHistory(): History
    {
        return $this->mockery(History::class);
    }

    /**
     * @param RangedWeaponCode $rangedWeaponCode
     * @return CurrentArmamentsWithSkills|MockInterface
     */
    private function createCurrentArmamentsWithSkills(RangedWeaponCode $rangedWeaponCode): CurrentArmamentsWithSkills
    {
        $currentArmamentsWithSkills = $this->mockery(CurrentArmamentsWithSkills::class);
        $currentArmamentsWithSkills->shouldReceive('getCurrentRangedWeapon')
            ->andReturn($rangedWeaponCode);
        return $currentArmamentsWithSkills;
    }

    /**
     * @return CurrentProperties|MockInterface
     */
    private function createCurrentProperties(): CurrentProperties
    {
        $currentProperties = $this->mockery(CurrentProperties::class);
        $currentProperties->shouldReceive('getCurrentStrength')
            ->andReturn(Strength::getIt(123));
        $currentProperties->shouldReceive('getCurrentSpeed')
            ->andReturn($this->createSpeed(456));
        return $currentProperties;
    }

    /**
     * @param int $value
     * @return Speed|MockInterface
     */
    private function createSpeed(int $value): Speed
    {
        $speed = $this->mockery(Speed::class);
        $speed->shouldReceive('getValue')
            ->andReturn($value);
        return $speed;
    }

    /**
     * @param array $namesToValues
     * @return CurrentValues|MockInterface
     */
    private function createCurrentValues(array $namesToValues): CurrentValues
    {
        $currentValues = $this->mockery(CurrentValues::class);
        $currentValues->shouldReceive('getCurrentValue')
            ->andReturnUsing(
                static function (string $name) use ($namesToValues) {
                    return $namesToValues[$name];
                }
            );
        return $currentValues;
    }

    /**
     * @return PreviousArmamentsWithSkills|MockInterface
     */
    private function createPreviousArmamentsWithSkills(): PreviousArmamentsWithSkills
    {
        return $this->mockery(PreviousArmamentsWithSkills::class);
    }

    /**
     * @return PreviousProperties|MockInterface
     */
    private function createPreviousProperties(): PreviousProperties
    {
        return $this->mockery(PreviousProperties::class);
    }

    /**
     * @param int $maximalRangeInMeters
     * @return Armourer|MockInterface
     */
    private function createArmourer(int $maximalRangeInMeters): Armourer
    {
        $armourer = $this->mockery(Armourer::class);
        $armourer->shouldReceive('getMaximalRangeWithWeaponlike')
            ->andReturn($maximalRange = $this->mockery(MaximalRange::class));
        $maximalRange->shouldReceive('getInMeters')
            ->with($this->type(Tables::class))
            ->andReturn($maximalRangeInMeters);
        return $armourer;
    }
}
