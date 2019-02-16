<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\BaseProperties\Strength;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Properties\Derived\Toughness;
use DrdPlus\Tables\Races\RacesTable;
use DrdPlus\Tables\Tables;
use Mockery\MockInterface;

class ToughnessTest extends AbstractDerivedPropertyTest
{
    protected function createIt(int $value): AbstractDerivedProperty
    {
        return Toughness::getIt(
            Strength::getIt($value),
            $raceCode = RaceCode::getIt(RaceCode::DWARF),
            $subRaceCode = SubRaceCode::getIt(SubRaceCode::DARK),
            $this->createTables($raceCode, $subRaceCode)
        );
    }

    /**
     * @param RaceCode $expectedRaceCode
     * @param SubRaceCode $expectedSubRaceCode
     * @return Tables|MockInterface
     */
    private function createTables(RaceCode $expectedRaceCode, SubRaceCode $expectedSubRaceCode): Tables
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getRacesTable')
            ->andReturn($racesTable = $this->mockery(RacesTable::class));
        $racesTable->shouldReceive('getToughness')
            ->with($expectedRaceCode, $expectedSubRaceCode)
            ->andReturn(0);
        return $tables;
    }
}