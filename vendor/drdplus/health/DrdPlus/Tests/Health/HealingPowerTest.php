<?php
namespace DrdPlus\Tests\Health;

use DrdPlus\Codes\Body\ActivityAffectingHealingCode;
use DrdPlus\Codes\Body\ConditionsAffectingHealingCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Health\HealingPower;
use DrdPlus\Properties\Derived\Toughness;
use DrdPlus\Tables\Body\Healing\HealingByActivityTable;
use DrdPlus\Tables\Body\Healing\HealingByConditionsTable;
use DrdPlus\Tables\Body\Healing\HealingConditionsPercents;
use DrdPlus\Tables\Measurements\Wounds\Wounds;
use DrdPlus\Tables\Measurements\Wounds\WoundsBonus;
use DrdPlus\Tables\Measurements\Wounds\WoundsTable;
use DrdPlus\Tables\Races\RacesTable;
use DrdPlus\Tables\Tables;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use Granam\Tests\Tools\TestWithMockery;

class HealingPowerTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it_for_treatment(): void
    {
        $healingPower = HealingPower::createForTreatment(
            123,
            $this->createToughness(3),
            $this->createTablesWithWoundsTable(126, 999)
        );
        self::assertSame(126, $healingPower->getValue());
        self::assertSame('126 (with heal up to 999)', (string)$healingPower);
        self::assertSame(999, $healingPower->getHealUpToWounds());
    }

    /**
     * @param $expectedWoundsBonus
     * @param $returnWoundsValue
     * @param \Closure $toBonus
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithWoundsTable(int $expectedWoundsBonus, int $returnWoundsValue, \Closure $toBonus = null)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getWoundsTable')
            ->andReturn($woundsTable = $this->mockery(WoundsTable::class));
        $woundsTable->shouldReceive('toWounds')
            ->atLeast()->once()
            ->andReturnUsing(function (WoundsBonus $woundBonus) use ($expectedWoundsBonus, $returnWoundsValue) {
                self::assertSame($expectedWoundsBonus, $woundBonus->getValue());
                $wounds = $this->mockery(Wounds::class);
                $wounds->shouldReceive('getValue')
                    ->andReturn($returnWoundsValue);
                $wounds->shouldReceive('getBonus')
                    ->andReturn($woundBonus);

                return $wounds;
            });
        if ($toBonus) {
            $woundsTable->shouldReceive('toBonus')
                ->atLeast()->once()
                ->andReturnUsing($toBonus);
        }

        return $tables;
    }

    /**
     * @param int $value
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
     * @test
     */
    public function I_can_use_it_for_regeneration(): void
    {
        foreach ([true, false] as $hasNativeRegeneration) {
            $tables = $this->createTablesWithWoundsTable($expectedValue = -7 + 123 + 456 + 789 + ($hasNativeRegeneration ? +4 : 0) - 5 /* toughness */, 112233);
            $tables->shouldReceive('getHealingByActivityTable')
                ->andReturn($this->createHealingByActivityTable('baz', 123));
            $healingConditionsPercents = $this->createHealingConditionsPercents();
            $tables->shouldReceive('getHealingByConditionsTable')
                ->andReturn($this->createHealingByConditionsTable('qux', $healingConditionsPercents, 456));
            $raceCode = $this->createRaceCode('foo');
            $subRaceCode = $this->createSubRaceCode('bar');
            $tables->shouldReceive('getRacesTable')
                ->andReturn($this->createRacesTable($raceCode, $subRaceCode, $hasNativeRegeneration));
            $healingPower = HealingPower::createForRegeneration(
                $raceCode,
                $subRaceCode,
                $this->createToughness(-5),
                $this->createActivityCode('baz'),
                $this->createConditionCode('qux'),
                $healingConditionsPercents,
                $this->createRoll2d6(789),
                $tables
            );
            self::assertSame($expectedValue, $healingPower->getValue());
            self::assertSame("$expectedValue (with heal up to 112233)", (string)$healingPower);
            self::assertSame(112233, $healingPower->getHealUpToWounds());
        }
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|RaceCode
     */
    private function createRaceCode($value)
    {
        $raceCode = $this->mockery(RaceCode::class);
        $raceCode->shouldReceive('getValue')
            ->andReturn($value);

        return $raceCode;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|SubRaceCode
     */
    private function createSubRaceCode($value)
    {
        $subRaceCode = $this->mockery(SubRaceCode::class);
        $subRaceCode->shouldReceive('getValue')
            ->andReturn($value);

        return $subRaceCode;
    }

    /**
     * @param RaceCode $expectedRaceCode
     * @param SubRaceCode $expectedSubRaceCode
     * @param $hasNativeRegeneration
     * @return RacesTable|\Mockery\MockInterface
     */
    private function createRacesTable(RaceCode $expectedRaceCode, SubRaceCode $expectedSubRaceCode, $hasNativeRegeneration)
    {
        $racesTable = $this->mockery(RacesTable::class);
        $racesTable->shouldReceive('hasNativeRegeneration')
            ->zeroOrMoreTimes()
            ->with($expectedRaceCode, $expectedSubRaceCode)
            ->andReturn($hasNativeRegeneration);

        return $racesTable;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|ActivityAffectingHealingCode
     */
    private function createActivityCode($value)
    {
        $activityCode = $this->mockery(ActivityAffectingHealingCode::class);
        $activityCode->shouldReceive('getValue')
            ->andReturn($value);

        return $activityCode;
    }

    /**
     * @param $expectedActivity
     * @param $bonus
     * @return \Mockery\MockInterface|HealingByActivityTable
     */
    private function createHealingByActivityTable($expectedActivity, $bonus)
    {
        $healingByActivityTable = $this->mockery(HealingByActivityTable::class);
        $healingByActivityTable->shouldReceive('getHealingBonusByActivity')
            ->zeroOrMoreTimes()
            ->with($expectedActivity)
            ->andReturn($bonus);

        return $healingByActivityTable;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|ConditionsAffectingHealingCode
     */
    private function createConditionCode($value)
    {
        $conditionsCode = $this->mockery(ConditionsAffectingHealingCode::class);
        $conditionsCode->shouldReceive('getValue')
            ->andReturn($value);

        return $conditionsCode;
    }

    /**
     * @return HealingConditionsPercents|\Mockery\MockInterface
     */
    private function createHealingConditionsPercents()
    {
        return $this->mockery(HealingConditionsPercents::class);
    }

    /**
     * @param $conditions
     * @param $percents
     * @param $healingByConditions
     * @return \Mockery\MockInterface|HealingByConditionsTable
     */
    private function createHealingByConditionsTable($conditions, $percents, $healingByConditions)
    {
        $healingByConditionsTable = $this->mockery(HealingByConditionsTable::class);
        $healingByConditionsTable->shouldReceive('getHealingBonusByConditions')
            ->zeroOrMoreTimes()
            ->with($conditions, $percents)
            ->andReturn($healingByConditions);

        return $healingByConditionsTable;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Roll2d6DrdPlus
     */
    private function createRoll2d6($value)
    {
        $roll2d6 = $this->mockery(Roll2d6DrdPlus::class);
        $roll2d6->shouldReceive('getValue')
            ->andReturn($value);

        return $roll2d6;
    }
}