<?php
namespace DrdPlus\Tests\Health;

use DrdPlus\Codes\Body\OrdinaryWoundOriginCode;
use DrdPlus\Codes\Body\SeriousWoundOriginCode;
use DrdPlus\Health\Afflictions\AfflictionByWound;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\SpecificAfflictions\Pain;
use DrdPlus\Health\HealingPower;
use DrdPlus\Health\Health;
use DrdPlus\Health\Inflictions\Glared;
use DrdPlus\Health\ReasonToRollAgainstMalusFromWounds;
use DrdPlus\Health\SeriousWound;
use DrdPlus\Health\Wound;
use DrdPlus\Health\WoundSize;
use DrdPlus\Lighting\Glare;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Derived\Toughness;
use DrdPlus\Properties\Derived\WoundBoundary;
use DrdPlus\Tables\Measurements\Wounds\Wounds;
use DrdPlus\Tables\Measurements\Wounds\WoundsBonus;
use DrdPlus\Tables\Measurements\Wounds\WoundsTable;
use DrdPlus\Tables\Tables;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use Granam\Tests\Tools\TestWithMockery;

class HealthTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(123));
        self::assertSame(369, $health->getRemainingHealthAmount($woundBoundary));
        self::assertSame(369, $health->getHealthMaximum($woundBoundary));
    }

    /**
     * @param WoundBoundary $woundBoundary
     * @return Health
     */
    private function createHealthToTest(WoundBoundary $woundBoundary): Health
    {
        $health = new Health();
        $this->assertUnwounded($health, $woundBoundary);

        return $health;
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|WoundBoundary
     */
    private function createWoundBoundary(int $value)
    {
        $woundBoundary = $this->mockery(WoundBoundary::class);
        $woundBoundary->shouldReceive('getValue')
            ->andReturn($value);

        return $woundBoundary;
    }

    private function assertUnwounded(Health $health, WoundBoundary $woundBoundary): void
    {
        self::assertSame($health->getGridOfWounds()->getWoundsPerRowMaximum($woundBoundary), $woundBoundary->getValue());
        self::assertSame($health->getGridOfWounds()->getWoundsPerRowMaximum($woundBoundary) * 3, $health->getHealthMaximum($woundBoundary));
        self::assertSame($health->getGridOfWounds()->getWoundsPerRowMaximum($woundBoundary) * 3, $health->getRemainingHealthAmount($woundBoundary));
        self::assertCount(0, $health->getUnhealedWounds());
        self::assertSame(0, $health->getUnhealedFreshOrdinaryWoundsSum());
        self::assertSame(0, $health->getUnhealedSeriousWoundsSum());
        self::assertSame(0, $health->getNumberOfSeriousInjuries());
        self::assertCount(0, $health->getAfflictions());
        self::assertSame(0, $health->getSignificantMalusFromPains($woundBoundary));
        self::assertCount(0, $health->getPains());
        self::assertTrue($health->isAlive($woundBoundary));
        self::assertTrue($health->isConscious($woundBoundary));
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        self::assertNull($health->getReasonToRollAgainstMalusFromWounds());
        self::assertSame(0, $health->getTreatmentBoundary()->getValue());
    }

    /**
     * @test
     * @dataProvider provideConsciousAndAlive
     * @param int $woundBoundaryValue
     * @param int $wound
     * @param bool $isConscious
     * @param bool $isAlive
     */
    public function I_can_easily_find_out_if_creature_is_conscious_and_alive(
        int $woundBoundaryValue,
        int $wound,
        bool $isConscious,
        bool $isAlive
    ): void
    {
        $woundBoundary = $this->createWoundBoundary($woundBoundaryValue);
        $health = $this->createHealthToTest($woundBoundary);
        $health->addWound(
            $this->createWoundSize($wound),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );

        self::assertSame($isConscious, $health->isConscious($woundBoundary));
        self::assertSame($isAlive, $health->isAlive($woundBoundary));
    }

    public function provideConsciousAndAlive(): array
    {
        return [
            [1, 0, true, true], // healthy
            [1, 1, true, true], // wounded
            [1, 2, false, true], // knocked down
            [1, 3, false, false], // dead
        ];
    }

    // TREATMENT BOUNDARY

    /**
     * @test
     */
    public function I_get_treatment_boundary_moved_to_reaming_wounds_on_ordinary_heal(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        self::assertSame(0, $health->getTreatmentBoundary()->getValue());
        $health->addWound($this->createWoundSize(4), SeriousWoundOriginCode::getMechanicalCutWoundOrigin(), $woundBoundary);
        self::assertSame(0, $health->getTreatmentBoundary()->getValue());
        $health->healFreshOrdinaryWounds($this->createHealingPower(1, 1), $woundBoundary);
        self::assertSame(3, $health->getTreatmentBoundary()->getValue());
        self::assertSame($health->getUnhealedWoundsSum(), $health->getTreatmentBoundary()->getValue());
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Toughness
     */
    private function createToughness(int $value)
    {
        $toughness = $this->mockery(Toughness::class);
        $toughness->shouldReceive('getValue')
            ->andReturn($value);

        return $toughness;
    }

    /**
     * @param int $woundsValue
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithWoundsTable(int $woundsValue)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getWoundsTable')
            ->andReturn($woundsTable = $this->mockery(WoundsTable::class));
        $woundsTable->shouldReceive('toWounds')
            ->andReturn($wounds = $this->mockery(Wounds::class));
        $wounds->shouldReceive('getValue')
            ->andReturn($woundsValue);
        $woundsTable->shouldReceive('toBonus')
            ->andReturn($woundsBonus = $this->mockery(WoundsBonus::class));
        /** just for @see \DrdPlus\Properties\Partials\WithHistoryTrait::extractArgumentsDescription */
        $woundsBonus->shouldReceive('getValue')
            ->andReturn(789);

        return $tables;
    }

    /**
     * @test
     */
    public function I_get_treatment_boundary_increased_by_serious_wound_immediately(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        self::assertSame(0, $health->getTreatmentBoundary()->getValue());
        $health->addWound($this->createWoundSize(7), SeriousWoundOriginCode::getMechanicalCutWoundOrigin(), $woundBoundary);
        self::assertSame(7, $health->getTreatmentBoundary()->getValue());
        self::assertSame($health->getUnhealedWoundsSum(), $health->getTreatmentBoundary()->getValue());
    }

    /**
     * @test
     */
    public function I_get_treatment_boundary_lowered_by_healed_serious_wound(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        self::assertSame(0, $health->getTreatmentBoundary()->getValue());
        $seriousWound = $health->addWound($this->createWoundSize(7), SeriousWoundOriginCode::getMechanicalCutWoundOrigin(), $woundBoundary);
        $health->healFreshSeriousWound(
            $seriousWound,
            $this->createHealingPower(5),
            $this->createToughness(123),
            $this->createTablesWithWoundsTable($woundBoundary->getValue())
        );
        self::assertSame(2, $health->getTreatmentBoundary()->getValue());
    }

    /**
     * @test
     */
    public function I_do_not_have_lowered_treatment_boundary_by_healed_ordinary_wound(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        $health->addWound($this->createWoundSize(3), SeriousWoundOriginCode::getMechanicalCrushWoundOrigin(), $woundBoundary);
        $health->addWound($this->createWoundSize(6), SeriousWoundOriginCode::getMechanicalCutWoundOrigin(), $woundBoundary);
        self::assertSame(6, $health->getTreatmentBoundary()->getValue());
        self::assertSame(9, $health->getUnhealedWoundsSum());
        $health->healFreshOrdinaryWounds($this->createHealingPower(999, 3), $woundBoundary);
        self::assertSame(6, $health->getTreatmentBoundary()->getValue());
        self::assertSame(6, $health->getUnhealedWoundsSum());
    }

    /**
     * @test
     */
    public function I_get_treatment_boundary_lowered_by_regenerated_amount(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        $health->addWound($this->createWoundSize(3), SeriousWoundOriginCode::getMechanicalCrushWoundOrigin(), $woundBoundary);
        $health->addWound($this->createWoundSize(6), SeriousWoundOriginCode::getMechanicalCutWoundOrigin(), $woundBoundary);
        self::assertSame(6, $health->getTreatmentBoundary()->getValue());
        $health->regenerate(HealingPower::createForTreatment(9, $this->createToughness(-1), Tables::getIt()), $woundBoundary);
        self::assertSame(
            1,
            $health->getTreatmentBoundary()->getValue(),
            'Both ordinary and serious wound should be regenerated, therefore treatment boundary should be moved by regenerating power'
        );
        self::assertSame(1, $health->getUnhealedWoundsSum());
    }

    // ROLL ON MALUS RESULT

    /**
     * @test
     * @dataProvider provideDecreasingRollAgainstMalusData
     * @param $willValue
     * @param $rollValue
     * @param $expectedMalus
     */
    public function I_should_roll_against_malus_from_wounds_because_of_new_wound($willValue, $rollValue, $expectedMalus): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        $health->addWound($this->createWoundSize(10), SeriousWoundOriginCode::getElementalWoundOrigin(), $woundBoundary);
        self::assertTrue($health->needsToRollAgainstMalusFromWounds());
        self::assertSame(ReasonToRollAgainstMalusFromWounds::getWoundReason(), $health->getReasonToRollAgainstMalusFromWounds());
        self::assertSame(
            $expectedMalus,
            $health->rollAgainstMalusFromWounds(
                $this->createWill($willValue),
                $this->createRoll2d6Plus($rollValue),
                $woundBoundary
            )
        );
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        self::assertNull($health->getReasonToRollAgainstMalusFromWounds());
    }

    public function provideDecreasingRollAgainstMalusData(): array
    {
        return [
            [7, 8, 0],
            [99, 99, 0],
            [6, 4, -1],
            [6, 8, -1],
            [3, 2, -2],
            [2, 3, -2],
            [1, 1, -3],
        ];
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|Will
     */
    private function createWill($value = null)
    {
        $will = $this->mockery(Will::class);
        if ($value !== null) {
            $will->shouldReceive('getValue')
                ->andReturn($value);
        }

        return $will;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Roll2d6DrdPlus
     */
    private function createRoll2d6Plus($value = null): Roll2d6DrdPlus
    {
        $roll = $this->mockery(Roll2d6DrdPlus::class);
        if ($value !== null) {
            $roll->shouldReceive('getValue')
                ->andReturn($value);
            $roll->shouldReceive('getRolledNumbers')
                ->andReturn([$value]);
        }

        return $roll;
    }

    /**
     * @test
     * @dataProvider provideIncreasingRollAgainstMalusData
     * @param $willValue
     * @param $rollValue
     * @param $expectedMalus
     */
    public function I_should_roll_against_malus_from_wounds_because_of_heal_of_ordinary_wound($willValue, $rollValue, $expectedMalus): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        $health->addWound($this->createWoundSize(4),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        $health->addWound($this->createWoundSize(4),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        $health->addWound($this->createWoundSize(4),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(-1),
            $this->createRoll2d6Plus(3),
            $woundBoundary
        ); // -3 malus as a result
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        $health->healFreshOrdinaryWounds($this->createHealingPower(1, 1), $woundBoundary);
        self::assertSame(ReasonToRollAgainstMalusFromWounds::getHealReason(), $health->getReasonToRollAgainstMalusFromWounds());
        self::assertSame(
            $expectedMalus,
            $health->rollAgainstMalusFromWounds(
                $this->createWill($willValue),
                $this->createRoll2d6Plus($rollValue),
                $woundBoundary
            )
        );
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        self::assertNull($health->getReasonToRollAgainstMalusFromWounds());
    }

    public function provideIncreasingRollAgainstMalusData(): array
    {
        return [
            [1, 1, -3],
            [3, 2, -2],
            [2, 3, -2],
            [6, 4, -1],
            [6, 8, -1],
            [7, 8, 0],
            [99, 99, 0],
        ];
    }

    /**
     * @test
     * @dataProvider provideIncreasingRollAgainstMalusData
     * @param $willValue
     * @param $rollValue
     * @param $expectedMalus
     */
    public function I_should_roll_against_malus_from_wounds_because_of_heal_of_serious_wound($willValue, $rollValue, $expectedMalus): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        $seriousWound = $health->addWound($this->createWoundSize(15),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(-1),
            $this->createRoll2d6Plus(3),
            $woundBoundary
        ); // -3 malus as a result
        $health->healFreshSeriousWound($seriousWound,
            $this->createHealingPower(1),
            $this->createToughness(123),
            $this->createTablesWithWoundsTable($woundBoundary->getValue())
        );
        self::assertSame(ReasonToRollAgainstMalusFromWounds::getHealReason(), $health->getReasonToRollAgainstMalusFromWounds());
        self::assertSame(
            $expectedMalus,
            $health->rollAgainstMalusFromWounds(
                $this->createWill($willValue),
                $this->createRoll2d6Plus($rollValue),
                $woundBoundary
            )
        );
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        self::assertNull($health->getReasonToRollAgainstMalusFromWounds());
    }

    /**
     * @test
     * @dataProvider provideIncreasingRollAgainstMalusData
     * @param $willValue
     * @param $rollValue
     * @param $expectedMalus
     */
    public function I_should_roll_against_malus_from_wounds_because_of_regeneration($willValue, $rollValue, $expectedMalus): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        $health->addWound($this->createWoundSize(15),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(-1),
            $this->createRoll2d6Plus(3),
            $woundBoundary
        ); // -3 malus as a result
        $health->regenerate($this->createHealingPower(5, 5), $woundBoundary);
        self::assertSame(ReasonToRollAgainstMalusFromWounds::getHealReason(), $health->getReasonToRollAgainstMalusFromWounds());
        self::assertSame(
            $expectedMalus,
            $health->rollAgainstMalusFromWounds(
                $this->createWill($willValue),
                $this->createRoll2d6Plus($rollValue),
                $woundBoundary
            )
        );
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        self::assertNull($health->getReasonToRollAgainstMalusFromWounds());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\UselessRollAgainstMalus
     */
    public function I_can_not_roll_on_malus_from_wounds_if_not_needed(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        $health->rollAgainstMalusFromWounds($this->createWill(),
            $this->createRoll2d6Plus(),
            $woundBoundary
        );
    }

    // ROLL ON MALUS EXPECTED

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function I_can_not_add_new_wound_if_roll_on_malus_expected(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        try {
            $health->addWound($this->createWoundSize(10),
                SeriousWoundOriginCode::getElementalWoundOrigin(),
                $woundBoundary
            );
        } catch (\Exception $exception) {
            self::fail('No exception expected so far: ' . $exception->getTraceAsString());
        }
        $health->addWound($this->createWoundSize(10),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function I_can_not_heal_fresh_ordinary_wounds_if_roll_on_malus_expected(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        try {
            $health->addWound(
                $this->createWoundSize(4),
                SeriousWoundOriginCode::getElementalWoundOrigin(),
                $woundBoundary
            );
            $health->addWound(
                $this->createWoundSize(4),
                SeriousWoundOriginCode::getElementalWoundOrigin(),
                $woundBoundary
            );
            $health->addWound(
                $this->createWoundSize(4),
                SeriousWoundOriginCode::getElementalWoundOrigin(),
                $woundBoundary
            );
        } catch (\Exception $exception) {
            self::fail('No exception expected so far: ' . $exception->getTraceAsString());
        }
        $health->healFreshOrdinaryWounds($this->createHealingPower(5), $woundBoundary);
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function I_can_not_heal_serious_wound_if_roll_on_malus_expected(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        try {
            $seriousWound = $health->addWound($this->createWoundSize(14),
                SeriousWoundOriginCode::getElementalWoundOrigin(),
                $woundBoundary
            );
        } catch (\Exception $exception) {
            self::fail('No exception expected so far: ' . $exception->getTraceAsString());
        }
        /** @noinspection PhpUndefinedVariableInspection */
        $health->healFreshSeriousWound($seriousWound,
            $this->createHealingPower(5),
            $this->createToughness(123),
            $this->createTablesWithWoundsTable($woundBoundary->getValue())
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function I_can_not_regenerate_if_roll_on_malus_expected(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        try {
            $health->addWound($this->createWoundSize(14),
                SeriousWoundOriginCode::getElementalWoundOrigin(),
                $woundBoundary
            );
        } catch (\Exception $exception) {
            self::fail('No exception expected so far: ' . $exception->getTraceAsString());
        }
        $health->regenerate($this->createHealingPower(5), $woundBoundary);
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function I_can_not_get_malus_from_wounds_if_roll_on_it_expected(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        try {
            $health->addWound(
                $this->createWoundSize(14),
                SeriousWoundOriginCode::getElementalWoundOrigin(),
                $woundBoundary
            );
        } catch (\Exception $exception) {
            self::fail('No exception expected so far: ' . $exception->getTraceAsString());
        }
        $health->getSignificantMalusFromPains($woundBoundary);
    }

    // MALUS CONDITIONAL CHANGES

    /**
     * @test
     * @dataProvider provideRollForMalus
     * @param int $willValue
     * @param int $rollValue
     * @param int $expectedMalus
     */
    public function Malus_can_increase_on_fresh_wound(int $willValue, int $rollValue, int $expectedMalus): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));

        $health->addWound(
            $this->createWoundSize(5),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        self::assertSame($expectedMalus, $health->rollAgainstMalusFromWounds($this->createWill($willValue),
            $this->createRoll2d6Plus($rollValue),
            $woundBoundary
        ));
        self::assertSame($expectedMalus, $health->getSignificantMalusFromPains($woundBoundary));

        for ($currentWillValue = $willValue, $currentRollValue = $rollValue;
             $currentRollValue > -2 && $currentWillValue > -2;
             $currentRollValue--, $currentWillValue--
        ) {
            $seriousWound = $health->addWound($this->createWoundSize(3),
                SeriousWoundOriginCode::getElementalWoundOrigin(),
                $woundBoundary
            );
            $currentlyExpectedMalus = max(0, min(3, (int)floor(($currentWillValue + $currentRollValue) / 5))) - 3; // 0; -1; -2; -3
            self::assertSame(
                $currentlyExpectedMalus, // malus can increase (be more negative)
                $health->rollAgainstMalusFromWounds(
                    $this->createWill($currentWillValue),
                    $this->createRoll2d6Plus($currentRollValue),
                    $woundBoundary
                ),
                "For will $currentWillValue and roll $currentRollValue has been expected malus $currentlyExpectedMalus"
            );
            self::assertSame($currentlyExpectedMalus, $health->getSignificantMalusFromPains($woundBoundary));
            $health->healFreshSeriousWound($seriousWound,
                $this->createHealingPower(5, 3),
                $this->createToughness(123),
                $this->createTablesWithWoundsTable($woundBoundary->getValue())
            ); // "resetting" currently given wound
            // low values to ensure untouched malus (should not be increased, therefore changed here at all, on heal)
            $health->rollAgainstMalusFromWounds($this->createWill(-1),
                $this->createRoll2d6Plus(-1),
                $woundBoundary
            );
        }
    }

    public function provideRollForMalus(): array
    {
        return [
            [1, 1, -3],
            [-5, -5, -3],
            [10, 5, 0],
            [15, 0, 0],
            [13, 1, -1],
            [2, 7, -2],
            [3, 7, -1],
            [3, 1, -3],
            [3, 2, -2],
        ];
    }

    /**
     * @test
     * @dataProvider provideRollForMalus
     * @param int $willValue
     * @param int $rollValue
     * @param int $expectedMalus
     */
    public function Malus_can_not_decrease_on_fresh_wound(int $willValue, int $rollValue, int $expectedMalus): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));

        $health->addWound($this->createWoundSize(5),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        self::assertSame($expectedMalus, $health->rollAgainstMalusFromWounds($this->createWill($willValue),
            $this->createRoll2d6Plus($rollValue),
            $woundBoundary
        ));
        self::assertSame($expectedMalus, $health->getSignificantMalusFromPains($woundBoundary));

        for ($currentWillValue = $willValue, $currentRollValue = $rollValue;
             $currentRollValue < 16 && $currentWillValue < 10;
             $currentRollValue++, $currentWillValue++
        ) {
            $seriousWound = $health->addWound($this->createWoundSize(3),
                SeriousWoundOriginCode::getElementalWoundOrigin(),
                $woundBoundary
            );
            self::assertSame(
                $expectedMalus, // malus should not be decreased (be closer to zero)
                $health->rollAgainstMalusFromWounds(
                    $this->createWill($currentWillValue),
                    $this->createRoll2d6Plus($currentRollValue),
                    $woundBoundary
                ),
                "Even for will $currentWillValue and roll $currentRollValue has been expected previous malus $expectedMalus"
            );
            self::assertSame($expectedMalus, $health->getSignificantMalusFromPains($woundBoundary));
            $health->healFreshSeriousWound($seriousWound,
                $this->createHealingPower(5, 3),
                $this->createToughness(123),
                $this->createTablesWithWoundsTable($woundBoundary->getValue())
            ); // "resetting" currently given wound
            // low values to ensure untouched malus (should not be increased, therefore changed here at all, on heal)
            $health->rollAgainstMalusFromWounds($this->createWill(-1),
                $this->createRoll2d6Plus(-1),
                $woundBoundary
            );
        }
    }

    /**
     * @test
     */
    public function Malus_is_not_increased_on_new_heal_by_worse_roll(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        self::assertSame(0, $health->getSignificantMalusFromPains($woundBoundary));

        // 3 ordinary wounds to reach some malus
        $health->addWound($this->createWoundSize(2),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        $health->addWound($this->createWoundSize(2),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        $health->addWound($this->createWoundSize(2),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(0),
            $this->createRoll2d6Plus(11),
            $woundBoundary
        );
        self::assertSame(-1, $health->getSignificantMalusFromPains($woundBoundary));
        self::assertSame(
            1,
            $health->healFreshOrdinaryWounds($this->createHealingPower(1, 1), $woundBoundary)
        );
        $health->rollAgainstMalusFromWounds($this->createWill(0),
            $this->createRoll2d6Plus(-2),
            $woundBoundary
        ); // much worse roll
        self::assertSame(-1, $health->getSignificantMalusFromPains($woundBoundary), 'Malus should not be increased');
    }

    // AFFLICTION

    /**
     * @test
     */
    public function I_can_add_affliction(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $wound = $health->addWound($this->createWoundSize(5),
            SeriousWoundOriginCode::getMechanicalCrushWoundOrigin(),
            $woundBoundary
        );
        $affliction = $this->createAffliction($wound);
        $health->addAffliction($affliction);
        self::assertCount(1, $health->getAfflictions());
        $afflictions = $health->getAfflictions();
        self::assertSame($affliction, \current($afflictions));
    }

    /**
     * @param SeriousWound $seriousWound
     * @param array $values
     * @return \Mockery\MockInterface|AfflictionByWound
     */
    private function createAffliction(SeriousWound $seriousWound, array $values = [])
    {
        $afflictionByWound = $this->mockery(AfflictionByWound::class);
        $afflictionByWound->shouldReceive('getSeriousWound')
            ->andReturn($seriousWound);
        $afflictionByWound->shouldReceive('getName')
            ->andReturn($this->mockery(AfflictionName::class));
        foreach ($values as $valueName => $value) {
            $afflictionByWound->shouldReceive('get' . ucfirst($valueName))
                ->andReturn($value);
        }

        return $afflictionByWound;
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\UnknownAfflictionOriginatingWound
     */
    public function I_can_not_add_affliction_of_unknown_wound(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $affliction = $this->createAffliction($this->createSeriousWound());
        $health->addAffliction($affliction);
    }

    /**
     * @return \Mockery\MockInterface|SeriousWound
     */
    private function createSeriousWound()
    {
        $wound = $this->mockery(SeriousWound::class);
        $wound->shouldReceive('getHealth')
            ->andReturn($this->mockery(Health::class));
        $wound->shouldReceive('getWoundOriginCode')
            ->andReturn(SeriousWoundOriginCode::getMechanicalCrushWoundOrigin());
        $wound->shouldReceive('__toString')
            ->andReturn('123');

        return $wound;
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\AfflictionIsAlreadyRegistered
     */
    public function I_can_not_add_same_affliction_twice(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $wound = $health->addWound(
            $this->createWoundSize(6),

            SeriousWoundOriginCode::getElementalWoundOrigin()
            ,
            $woundBoundary
        );
        $affliction = $this->createAffliction($wound);
        try {
            $health->addAffliction($affliction);
        } catch (\Exception $exception) {
            self::fail('No exception expected so far: ' . $exception->getTraceAsString());
        }
        $health->addAffliction($affliction);
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\UnknownAfflictionOriginatingWound
     */
    public function I_can_not_add_affliction_with_to_health_unknown_wound(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $seriousWound = $health->addWound(
            $this->createWoundSize(6),

            SeriousWoundOriginCode::getElementalWoundOrigin()
            ,
            $woundBoundary
        );
        $anotherHealth = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $anotherHealth->addAffliction($this->createAffliction($seriousWound));
    }

    // NEW WOUND

    /**
     * @test
     */
    public function I_can_be_ordinary_wounded(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $ordinaryWound = $health->addWound(
            $this->createWoundSize(2),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        self::assertInstanceOf(Wound::class, $ordinaryWound);
        self::assertSame(2, $ordinaryWound->getValue());
        self::assertSame(
            OrdinaryWoundOriginCode::getIt(),
            $ordinaryWound->getWoundOriginCode(),
            'The ordinary wound origin should be used on such small wound'
        );
        self::assertCount(1, $health->getUnhealedWounds());
        $unhealedWounds = $health->getUnhealedWounds();
        self::assertSame($ordinaryWound, \end($unhealedWounds));
        self::assertSame(13, $health->getRemainingHealthAmount($woundBoundary));
        self::assertSame(2, $health->getUnhealedFreshOrdinaryWoundsSum());
        self::assertSame(0, $health->getSignificantMalusFromPains($woundBoundary));
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        self::assertNull($health->getReasonToRollAgainstMalusFromWounds());

        $anotherOrdinaryWound = $health->addWound(
            $this->createWoundSize(1),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        self::assertInstanceOf(Wound::class, $anotherOrdinaryWound);
        self::assertSame(1, $anotherOrdinaryWound->getValue());
        self::assertSame(
            OrdinaryWoundOriginCode::getIt(),
            $anotherOrdinaryWound->getWoundOriginCode(),
            'The ordinary wound origin should be used on such small wound'
        );
        self::assertCount(2, $health->getUnhealedWounds());
        $unhealedWounds = $health->getUnhealedWounds();
        self::assertSame($anotherOrdinaryWound, \end($unhealedWounds));
        self::assertSame([$ordinaryWound, $anotherOrdinaryWound], $health->getUnhealedWounds());
        self::assertSame(3, $health->getUnhealedFreshOrdinaryWoundsSum());
        self::assertSame(12, $health->getRemainingHealthAmount($woundBoundary));
        self::assertSame(0, $health->getSignificantMalusFromPains($woundBoundary));
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        self::assertNull($health->getReasonToRollAgainstMalusFromWounds());
    }

    /**
     * @test
     */
    public function I_can_be_ordinary_healed(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(7));

        $health->addWound($this->createWoundSize(1),
            SeriousWoundOriginCode::getElementalWoundOrigin(),
            $woundBoundary
        );
        $health->addWound($this->createWoundSize(3),
            SeriousWoundOriginCode::getMechanicalCrushWoundOrigin(),
            $woundBoundary
        );
        $health->addWound($this->createWoundSize(2),
            SeriousWoundOriginCode::getMechanicalStabWoundOrigin(),
            $woundBoundary
        );

        self::assertSame(15, $health->getRemainingHealthAmount($woundBoundary));
        self::assertSame(
            4/** power of 4 - 3 (toughness) = 1 heals up to 4 wounds, @see WoundsTable and related bonus-to-value conversion */,
            $health->healFreshOrdinaryWounds(
                HealingPower::createForTreatment(4, $this->createToughness(-3), Tables::getIt()),
                $woundBoundary
            )
        );
        self::assertSame(19, $health->getRemainingHealthAmount($woundBoundary));
        self::assertSame(2, $health->getUnhealedWoundsSum());
        self::assertSame(0, $health->getUnhealedFreshOrdinaryWoundsSum(), 'All ordinary wounds should become "old" after heal');
        self::assertSame(0, $health->getUnhealedSeriousWoundsSum());
        self::assertSame(0, $health->getNumberOfSeriousInjuries());
        self::assertSame(0, $health->getSignificantMalusFromPains($woundBoundary));
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        self::assertNull($health->getReasonToRollAgainstMalusFromWounds());
        self::assertSame(
            0,
            $health->healFreshOrdinaryWounds($this->createHealingPower(10, 0), $woundBoundary),
            'Nothing should be healed as a "new ordinary wound: because of treatment boundary'
        );
        self::assertSame(19, $health->getRemainingHealthAmount($woundBoundary));
        self::assertSame(2, $health->getUnhealedWoundsSum());
    }

    /**
     * @param $healUpTo
     * @param $expectedHealedAmount
     * @return \Mockery\MockInterface|HealingPower
     */
    private function createHealingPower($healUpTo = null, $expectedHealedAmount = null)
    {
        $healingPower = $this->mockery(HealingPower::class);
        if ($healUpTo !== null) {
            $healingPower->shouldReceive('getHealUpToWounds')
                ->andReturn($healUpTo);
        }
        if ($expectedHealedAmount !== null) {
            $decreasedHealingPower = $this->mockery(HealingPower::class);
            $decreasedHealingPower->shouldReceive('getHealUpToWounds')
                ->andReturn(0);
        }

        return $healingPower;
    }

    /**
     * @test
     */
    public function I_can_be_seriously_wounded(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(6));

        $seriousWoundByStab = $health->addWound(
            $this->createWoundSize(3),
            $seriousWoundOrigin = SeriousWoundOriginCode::getMechanicalStabWoundOrigin(),
            $woundBoundary
        );
        self::assertInstanceOf(Wound::class, $seriousWoundByStab);
        self::assertSame(3, $seriousWoundByStab->getValue());
        self::assertSame($seriousWoundOrigin, $seriousWoundByStab->getWoundOriginCode());
        self::assertCount(1, $health->getUnhealedWounds());
        self::assertCount(1, $health->getUnhealedFreshWounds());
        self::assertCount(0, $health->getUnhealedOldWounds());
        $unhealedWounds = $health->getUnhealedWounds();
        self::assertSame($seriousWoundByStab, \end($unhealedWounds));
        $unhealedFreshWounds = $health->getUnhealedFreshWounds();
        self::assertSame($seriousWoundByStab, \end($unhealedFreshWounds));
        self::assertSame(15, $health->getRemainingHealthAmount($woundBoundary));
        self::assertSame(0, $health->getUnhealedFreshOrdinaryWoundsSum());
        self::assertSame(3, $health->getUnhealedFreshSeriousWoundsSum());
        self::assertSame(3, $health->getUnhealedSeriousWoundsSum());
        self::assertSame(0, $health->getSignificantMalusFromPains($woundBoundary), 'There are not enough wounds to suffer from them yet.');
        self::assertFalse($health->needsToRollAgainstMalusFromWounds());
        self::assertNull($health->getReasonToRollAgainstMalusFromWounds());

        $seriousWoundByPsyche = $health->addWound(
            $this->createWoundSize(5),
            $seriousWoundOrigin = SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );
        self::assertInstanceOf(Wound::class, $seriousWoundByPsyche);
        self::assertSame(5, $seriousWoundByPsyche->getValue());
        self::assertTrue($seriousWoundByPsyche->isSerious());
        self::assertSame($seriousWoundOrigin, $seriousWoundByPsyche->getWoundOriginCode());
        self::assertCount(2, $health->getUnhealedWounds());
        self::assertSame(0, $health->getUnhealedFreshOrdinaryWoundsSum());
        self::assertSame(8, $health->getUnhealedSeriousWoundsSum());
        self::assertSame(8, $health->getUnhealedFreshSeriousWoundsSum());
        self::assertSame(8, $health->getUnhealedWoundsSum());
        self::assertSame(10, $health->getRemainingHealthAmount($woundBoundary));
        self::assertTrue($health->needsToRollAgainstMalusFromWounds());
        self::assertSame(ReasonToRollAgainstMalusFromWounds::getWoundReason(), $health->getReasonToRollAgainstMalusFromWounds());
        $woundSum = 0;
        $collectedWounds = [];
        foreach ($health->getUnhealedWounds() as $unhealedWound) {
            self::assertInstanceOf(Wound::class, $unhealedWound);
            self::assertLessThanOrEqual(5, $unhealedWound->getValue());
            $woundSum += $unhealedWound->getValue();
            $collectedWounds[] = $unhealedWound;
        }
        $collectedWounds = $this->sortObjects($collectedWounds);
        $unhealedWounds = $this->sortObjects($health->getUnhealedWounds());
        self::assertSame($unhealedWounds, $collectedWounds);
        self::assertCount(2, $health->getUnhealedWounds());
        self::assertSame(8, $woundSum);
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|WoundSize
     */
    private function createWoundSize($value)
    {
        $woundSize = $this->mockery(WoundSize::class);
        $woundSize->shouldReceive('getValue')
            ->andReturn($value);

        return $woundSize;
    }

    private function sortObjects(array $objects): array
    {
        \usort($objects, function ($object1, $object2) {
            return \strcasecmp(\spl_object_hash($object1), \spl_object_hash($object2));
        });

        return $objects;
    }

    /**
     * @test
     */
    public function I_can_get_healed_serious_wounds(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(6));

        $seriousWoundByStab = $health->addWound(
            $this->createWoundSize(3),
            $seriousWoundOrigin = SeriousWoundOriginCode::getMechanicalStabWoundOrigin(),
            $woundBoundary
        );
        $seriousWoundByPsyche = $health->addWound(
            $this->createWoundSize(5),
            $seriousWoundOrigin = SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );

        self::assertSame(
            -3,
            $health->rollAgainstMalusFromWounds($this->createWill(-1), $this->createRoll2d6Plus(1), $woundBoundary)
        );
        self::assertSame(
            0,
            $health->healFreshOrdinaryWounds($this->createHealingPower(1, 0), $woundBoundary),
            'Nothing should be healed because there is no ordinary wound'
        );
        self::assertSame(8, $health->getUnhealedWoundsSum());
        self::assertCount(2, $health->getUnhealedWounds());
        self::assertSame(10, $health->getRemainingHealthAmount($woundBoundary));
        self::assertSame(8, $health->getUnhealedSeriousWoundsSum());
        self::assertSame(8, $health->getUnhealedFreshSeriousWoundsSum());
        self::assertSame(0, $health->getUnhealedOldWoundsSum());
        self::assertSame(3, $health->healFreshSeriousWound($seriousWoundByPsyche,
            $this->createHealingPower(3, 3),
            $this->createToughness(123),
            $this->createTablesWithWoundsTable($woundBoundary->getValue())
        ));
        self::assertSame(13, $health->getRemainingHealthAmount($woundBoundary));
        self::assertCount(2, $health->getUnhealedWounds());
        self::assertSame(5, $health->getUnhealedWoundsSum());
        self::assertSame(0, $health->getUnhealedFreshOrdinaryWoundsSum());
        self::assertSame(5, $health->getUnhealedSeriousWoundsSum());
        self::assertSame(3, $health->getUnhealedFreshSeriousWoundsSum());
        self::assertSame(2, $health->getUnhealedOldSeriousWoundsSum());
        self::assertSame(2, $health->getNumberOfSeriousInjuries(), 'Both serious wounds are still unhealed');
        self::assertSame(0, $health->getSignificantMalusFromPains($woundBoundary), 'Malus should be gone because of low damage after heal');

        self::assertSame(3, $health->healFreshSeriousWound($seriousWoundByStab,
            $this->createHealingPower(10, 3),
            $this->createToughness(123),
            $this->createTablesWithWoundsTable($woundBoundary->getValue())
        ));
        self::assertSame(16, $health->getRemainingHealthAmount($woundBoundary));
        self::assertCount(1, $health->getUnhealedWounds());
        self::assertSame(2, $health->getUnhealedWoundsSum());
        self::assertSame(0, $health->getUnhealedFreshOrdinaryWoundsSum());
        self::assertSame(0, $health->getUnhealedFreshSeriousWoundsSum());
        self::assertSame(2, $health->getUnhealedOldSeriousWoundsSum());
        self::assertSame(2, $health->getUnhealedSeriousWoundsSum());
        self::assertSame(1, $health->getNumberOfSeriousInjuries(), 'Single serious wound is unhealed');
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\UnknownSeriousWoundToHeal
     */
    public function I_can_not_heal_serious_wound_from_different_health(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $seriousWound = $health->addWound($this->createWoundSize(5),
            SeriousWoundOriginCode::getMechanicalCutWoundOrigin(),
            $woundBoundary
        );
        $anotherHealth = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(3));
        $anotherHealth->healFreshSeriousWound($seriousWound,
            $this->createHealingPower(),
            $this->createToughness(123),
            $this->createTablesWithWoundsTable($woundBoundary->getValue())
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\UnknownSeriousWoundToHeal
     * @throws \ReflectionException
     */
    public function I_can_not_heal_serious_wound_not_created_by_current_health(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $healthReflection = new \ReflectionClass($health);
        $openForNewWound = $healthReflection->getProperty('openForNewWound');
        $openForNewWound->setAccessible(true);
        $openForNewWound->setValue($health, true);
        $seriousWound = new SeriousWound($health, $this->createWoundSize(5), SeriousWoundOriginCode::getMechanicalCutWoundOrigin());
        $health->healFreshSeriousWound($seriousWound,
            $this->createHealingPower(),
            $this->createToughness(123),
            $this->createTablesWithWoundsTable($woundBoundary->getValue())
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\ExpectedFreshWoundToHeal
     */
    public function I_can_not_heal_old_serious_wound(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $seriousWound = $health->addWound(
            $this->createWoundSize(5),
            SeriousWoundOriginCode::getMechanicalCutWoundOrigin(),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(0),
            $this->createRoll2d6Plus(10),
            $woundBoundary
        );
        self::assertTrue($seriousWound->isSerious());
        $seriousWound->setOld();
        self::assertTrue($seriousWound->isOld());
        $health->healFreshSeriousWound($seriousWound,
            $this->createHealingPower(),
            $this->createToughness(123),
            $this->createTablesWithWoundsTable($woundBoundary->getValue())
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\ExpectedFreshWoundToHeal
     */
    public function I_can_not_heal_already_treated_serious_wound(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(5));
        $seriousWound = $health->addWound(
            $this->createWoundSize(5),

            SeriousWoundOriginCode::getMechanicalCutWoundOrigin()
            ,
            $woundBoundary
        );
        self::assertTrue($seriousWound->isSerious());
        $health->rollAgainstMalusFromWounds($this->createWill(123),
            $this->createRoll2d6Plus(321),
            $woundBoundary
        );
        try {
            $health->healFreshSeriousWound($seriousWound,
                $this->createHealingPower(3, 3),
                $this->createToughness(123),
                $this->createTablesWithWoundsTable($woundBoundary->getValue())
            );
        } catch (\Exception $exception) {
            self::fail('No exception expected so far: ' . $exception->getMessage() . "\n" . $exception->getTraceAsString());
        }
        self::assertTrue($seriousWound->isOld());
        $health->healFreshSeriousWound($seriousWound,
            $this->createHealingPower(),
            $this->createToughness(123),
            $this->createTablesWithWoundsTable($woundBoundary->getValue())
        );
    }

    /**
     * @test
     */
    public function I_can_ask_it_if_has_fresh_wounds(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        self::assertFalse($health->hasFreshWounds());
        $health->addWound(
            $this->createWoundSize(1),
            SeriousWoundOriginCode::getMechanicalCrushWoundOrigin(),
            $woundBoundary
        );
        self::assertTrue($health->hasFreshWounds(), 'There should be a fresh wound');
    }

    /**
     * @test
     */
    public function I_can_be_wounded_both_ordinary_and_seriously(): void
    {
        $ordinaryWoundsSize = 0;
        $seriousWoundsSize = 0;
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(10));
        $firstOrdinaryWound = $health->addWound(
            $this->createWoundSize(1),
            SeriousWoundOriginCode::getMechanicalCrushWoundOrigin(),
            $woundBoundary
        );
        self::assertTrue($firstOrdinaryWound->isOrdinary(), 'That wounds expected to be ordinary for testing purposes');
        $ordinaryWoundsSize += $firstOrdinaryWound->getValue();
        $secondOrdinaryWound = $health->addWound(
            $this->createWoundSize(1),
            SeriousWoundOriginCode::getMechanicalCrushWoundOrigin(),
            $woundBoundary
        );
        self::assertTrue($secondOrdinaryWound->isOrdinary(), 'That wounds expected to be ordinary for testing purposes');
        $ordinaryWoundsSize += $secondOrdinaryWound->getValue();
        $firstSeriousWound = $health->addWound(
            $this->createWoundSize(5),
            SeriousWoundOriginCode::getMechanicalCrushWoundOrigin(),
            $woundBoundary
        );
        $seriousWoundsSize += $firstSeriousWound->getValue();
        self::assertTrue($firstSeriousWound->isSerious(), 'That wounds expected to be serious for testing purposes');
        $secondSeriousWound = $health->addWound(
            $this->createWoundSize(6),
            SeriousWoundOriginCode::getMechanicalCrushWoundOrigin(),
            $woundBoundary
        );
        $seriousWoundsSize += $secondSeriousWound->getValue();
        self::assertTrue($secondSeriousWound->isSerious(), 'That wounds expected to be serious for testing purposes');
        // total
        self::assertSame($seriousWoundsSize, $health->getTreatmentBoundary()->getValue(), 'Treatment boundary should be moved on sum of serious wounds');
        self::assertSame($ordinaryWoundsSize + $seriousWoundsSize, $health->getUnhealedWoundsSum());
        // fresh
        self::assertSame($ordinaryWoundsSize + $seriousWoundsSize, $health->getUnhealedFreshWoundsSum());
        self::assertSame($ordinaryWoundsSize, $health->getUnhealedFreshOrdinaryWoundsSum());
        self::assertSame($seriousWoundsSize, $health->getUnhealedFreshSeriousWoundsSum());
        // old
        self::assertSame(0, $health->getUnhealedOldWoundsSum());
        self::assertSame(0, $health->getUnhealedOldOrdinaryWoundsSum());
        self::assertSame(0, $health->getUnhealedOldSeriousWoundsSum());
        // ordinary vs serious
        self::assertSame($ordinaryWoundsSize, $health->getUnhealedOrdinaryWoundsSum());
        self::assertSame($seriousWoundsSize, $health->getUnhealedSeriousWoundsSum());
        $health->rollAgainstMalusFromWounds($this->createWill(1), $this->createRoll2d6Plus(5), $woundBoundary);
        self::assertSame(
            0, // nothing healed because of too low healing power
            $health->healFreshOrdinaryWounds($this->createHealingPower(-21, 0), $woundBoundary)
        );
        // all ordinary wounds should become old as we tried to heal them (even if unsuccessfully)
        self::assertSame(0, $health->getUnhealedFreshOrdinaryWoundsSum(), 'All ordinary wounds should be marked as old');
        self::assertSame(
            $health->getUnhealedFreshOrdinaryWoundsSum(),
            $health->getUnhealedWoundsSum() - $health->getTreatmentBoundary()->getValue()
        );
        self::assertSame($ordinaryWoundsSize, $health->getUnhealedOldWoundsSum(), 'All ordinary wounds should be marked as old');
        self::assertSame($ordinaryWoundsSize, $health->getUnhealedOldOrdinaryWoundsSum(), 'All ordinary wounds should be marked as old');
        // nothing should change on serious wounds so far
        self::assertSame($seriousWoundsSize, $health->getUnhealedSeriousWoundsSum());
        self::assertSame($seriousWoundsSize, $health->getUnhealedFreshSeriousWoundsSum());
        self::assertSame(0, $health->getUnhealedOldSeriousWoundsSum());
        self::assertSame(
            0, // nothing healed because of too low healing power
            $health->healFreshSeriousWound(
                $firstSeriousWound,
                $this->createHealingPower(-21, 0),
                $this->createToughness(123),
                $this->createTablesWithWoundsTable($woundBoundary->getValue())
            )
        );
        // first serious wound should be marked as old
        self::assertSame($seriousWoundsSize, $health->getUnhealedSeriousWoundsSum());
        self::assertSame(
            $secondSeriousWound->getValue(), // this one is unhealed
            $health->getUnhealedFreshSeriousWoundsSum()
        );
        self::assertSame(
            $firstSeriousWound->getValue(), // this one was attempted to be healed, so is old
            $health->getUnhealedOldSeriousWoundsSum()
        );
    }

    /**
     * @test
     */
    public function I_get_highest_malus_from_wound_and_pains(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(12));
        $damnSeriousWound = $health->addWound($this->createWoundSize(15),
            SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(1),
            $this->createRoll2d6Plus(7),
            $woundBoundary
        );
        self::assertSame(-2, $health->getSignificantMalusFromPains($woundBoundary));
        $health->addAffliction($this->createAffliction($damnSeriousWound));
        self::assertSame(-2, $health->getSignificantMalusFromPains($woundBoundary));
        $health->addAffliction($this->createPain($damnSeriousWound, ['malusToActivities' => -5]));
        self::assertSame(-5, $health->getSignificantMalusFromPains($woundBoundary));
    }

    /**
     * @param SeriousWound $seriousWound
     * @param array $maluses
     * @return \Mockery\MockInterface|Pain
     */
    private function createPain(SeriousWound $seriousWound, array $maluses = [])
    {
        $pain = $this->mockery(Pain::class);
        $pain->shouldReceive('getSeriousWound')
            ->andReturn($seriousWound);
        foreach ($maluses as $nameOfValue => $otherValue) {
            $pain->shouldReceive('get' . ucfirst($nameOfValue))
                ->andReturn($otherValue);
        }

        return $pain;
    }

    /**
     * @test
     */
    public function I_can_get_all_pains_and_afflictions(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(123));
        $seriousWound = $health->addWound($this->createWoundSize(70),
            SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );
        $health->addAffliction($firstPain = $this->createPain($seriousWound, ['malusToActivities' => -10]));
        $health->addAffliction($someAffliction = $this->createAffliction($seriousWound));
        $health->addAffliction($secondPain = $this->createPain($seriousWound, ['malusToActivities' => -20]));
        $health->addAffliction($thirdPain = $this->createPain($seriousWound, ['malusToActivities' => -30]));
        self::assertSame($this->sortObjects([$firstPain, $secondPain, $thirdPain]), $this->sortObjects($health->getPains()));
        self::assertSame($this->sortObjects([$firstPain, $secondPain, $someAffliction, $thirdPain]), $this->sortObjects($health->getAfflictions()));
    }

    /**
     * @test
     */
    public function I_can_get_strength_malus_from_afflictions(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(123));
        $seriousWound = $health->addWound($this->createWoundSize(70),
            SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );
        $health->addAffliction($this->createPain($seriousWound, ['strengthMalus' => -4]));
        $health->addAffliction($this->createAffliction($seriousWound, ['strengthMalus' => -1]));
        $health->addAffliction($this->createPain($seriousWound, ['strengthMalus' => 123]));

        self::assertSame(118, $health->getStrengthMalusFromAfflictions());
    }

    /**
     * @test
     */
    public function I_can_get_agility_malus_from_afflictions(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(123));
        $seriousWound = $health->addWound($this->createWoundSize(70),
            SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );
        $health->addAffliction($this->createPain($seriousWound, ['agilityMalus' => -1]));
        $health->addAffliction($this->createAffliction($seriousWound, ['agilityMalus' => -2]));
        $health->addAffliction($this->createPain($seriousWound, ['agilityMalus' => -3]));

        self::assertSame(-6, $health->getAgilityMalusFromAfflictions());
    }

    /**
     * @test
     */
    public function I_can_get_knack_malus_from_afflictions(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(123));
        $seriousWound = $health->addWound($this->createWoundSize(70),
            SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );
        $health->addAffliction($this->createPain($seriousWound, ['knackMalus' => -8]));
        $health->addAffliction($this->createAffliction($seriousWound, ['knackMalus' => -15]));
        $health->addAffliction($this->createPain($seriousWound, ['knackMalus' => -1]));

        self::assertSame(-24, $health->getKnackMalusFromAfflictions());
    }

    /**
     * @test
     */
    public function I_can_get_will_malus_from_afflictions(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(123));
        $seriousWound = $health->addWound($this->createWoundSize(70),
            SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );
        $health->addAffliction($this->createPain($seriousWound, ['willMalus' => -3]));
        $health->addAffliction($this->createAffliction($seriousWound, ['willMalus' => -2]));
        $health->addAffliction($this->createPain($seriousWound, ['willMalus' => -5]));

        self::assertSame(-10, $health->getWillMalusFromAfflictions());
    }

    /**
     * @test
     */
    public function I_can_get_intelligence_malus_from_afflictions(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(123));
        $seriousWound = $health->addWound($this->createWoundSize(70),
            SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );
        $health->addAffliction($this->createPain($seriousWound, ['intelligenceMalus' => 0]));
        $health->addAffliction($this->createAffliction($seriousWound, ['intelligenceMalus' => 1]));
        $health->addAffliction($this->createPain($seriousWound, ['intelligenceMalus' => 0]));
        $health->addAffliction($this->createPain($seriousWound, ['intelligenceMalus' => -6]));

        self::assertSame(-5, $health->getIntelligenceMalusFromAfflictions());
    }

    /**
     * @test
     */
    public function I_can_get_charisma_malus_from_afflictions(): void
    {
        $health = $this->createHealthToTest($woundBoundary = $this->createWoundBoundary(123));
        $seriousWound = $health->addWound($this->createWoundSize(70),
            SeriousWoundOriginCode::getPsychicalWoundOrigin(),
            $woundBoundary
        );
        $health->addAffliction($this->createPain($seriousWound, ['charismaMalus' => -5]));
        $health->addAffliction($this->createAffliction($seriousWound, ['charismaMalus' => -2]));

        self::assertSame(-7, $health->getCharismaMalusFromAfflictions());
    }

    /**
     * @test
     */
    public function I_can_be_glared(): void
    {
        $health = new Health();
        self::assertEquals(Glared::createWithoutGlare($health), $health->getGlared());
        $health->inflictByGlare($glare = $this->createGlare());
        self::assertEquals(Glared::createFromGlare($glare, $health), $health->getGlared());
        $previousGlared = $health->getGlared();
        $health->inflictByGlare($this->createGlare());
        self::assertNotSame($previousGlared, $health->getGlared());
    }

    /**
     * @param int $malus
     * @param bool $isShined
     * @return \Mockery\MockInterface|Glare
     */
    private function createGlare($malus = -123, $isShined = true)
    {
        $glare = $this->mockery(Glare::class);
        $glare->shouldReceive('getMalus')
            ->andReturn($malus);
        $glare->shouldReceive('isShined')
            ->andReturn($isShined);

        return $glare;
    }

    /**
     * @test
     */
    public function I_can_easily_find_out_if_may_have_malus_from_wounds(): void
    {
        $health = new Health();
        self::assertFalse($health->mayHaveMalusFromWounds($woundBoundary = $this->createWoundBoundary(10)));
        $health->addWound(
            $this->createWoundSize($woundBoundary->getValue() - 1),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        self::assertFalse($health->mayHaveMalusFromWounds($woundBoundary));
        self::assertTrue($health->mayHaveMalusFromWounds($this->createWoundBoundary($woundBoundary->getValue() - 1)));
        self::assertFalse($health->mayHaveMalusFromWounds($woundBoundary));
        $health->addWound(
            $this->createWoundSize(1),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        self::assertTrue($health->mayHaveMalusFromWounds($woundBoundary));
    }

    /**
     * @test
     */
    public function I_can_ask_it_if_may_suffer_from_wounds(): void
    {
        $woundBoundary = $this->createWoundBoundary(5);
        $health = $this->createHealthToTest($woundBoundary);
        self::assertFalse($health->maySufferFromWounds($woundBoundary));
        $health->addWound(
            $this->createWoundSize(4),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        self::assertFalse($health->maySufferFromWounds($woundBoundary));
        $health->addWound(
            $this->createWoundSize(1),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(999), $this->createRoll2d6Plus(999), $woundBoundary);
        self::assertSame(0, $health->getSignificantMalusFromPains($woundBoundary), 'No malus expected due to a really high roll and will');
        self::assertTrue(
            $health->maySufferFromWounds($woundBoundary),
            'There should be already a chance to suffer from wounds as the fatigue boundary is same as fatigue (first row is filled)'
        );
        $health->addWound(
            $this->createWoundSize(4),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(999), $this->createRoll2d6Plus(999), $woundBoundary);
        self::assertTrue(
            $health->maySufferFromWounds($woundBoundary),
            'There should be a chance to suffer from wounds as two rows are filled'
        );
        self::assertTrue($health->isConscious($woundBoundary));
        $health->addWound(
            $this->createWoundSize(1),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        self::assertFalse($health->isConscious($woundBoundary));
        self::assertFalse(
            $health->maySufferFromWounds($woundBoundary),
            'I should not suffer from wounds as I am unconscious'
        );
    }

    /**
     * @test
     */
    public function I_can_ask_it_if_I_am_suffering_from_wounds(): void
    {
        $woundBoundary = $this->createWoundBoundary(5);
        $health = $this->createHealthToTest($woundBoundary);
        self::assertFalse($health->mayHaveMalusFromWounds($woundBoundary));
        $health->addWound(
            $this->createWoundSize(4),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        self::assertFalse($health->mayHaveMalusFromWounds($woundBoundary));
        $health->addWound(
            $this->createWoundSize(1),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(999), $this->createRoll2d6Plus(999), $woundBoundary);
        self::assertSame(0, $health->getSignificantMalusFromPains($woundBoundary), 'No malus expected due to a really high roll and will');
        self::assertTrue(
            $health->mayHaveMalusFromWounds($woundBoundary),
            'There should be already a chance to suffer from wounds as the fatigue boundary is same as fatigue (first row is filled)'
        );
        $health->addWound(
            $this->createWoundSize(4),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        $health->rollAgainstMalusFromWounds($this->createWill(999), $this->createRoll2d6Plus(999), $woundBoundary);
        self::assertTrue(
            $health->mayHaveMalusFromWounds($woundBoundary),
            'There should be a chance to suffer from wounds as two rows are filled'
        );
        self::assertTrue($health->isConscious($woundBoundary));
        $health->addWound(
            $this->createWoundSize(1),
            SeriousWoundOriginCode::getIt(SeriousWoundOriginCode::PSYCHICAL),
            $woundBoundary
        );
        self::assertFalse($health->isConscious($woundBoundary));
        self::assertTrue(
            $health->mayHaveMalusFromWounds($woundBoundary),
            'I should still have (non-applicable) malus fatigue even as unconscious'
        );
    }
}