<?php declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\Traps;

use DrdPlus\Codes\Properties\RemarkableSenseCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\RollsOn\Traps\BonusFromUsedRemarkableSense;
use DrdPlus\Tables\Races\RacesTable;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class BonusFromUsedRemarkableSenseTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $zeroBonusFromUsedRemarkableSense = new BonusFromUsedRemarkableSense(
            $raceCode = $this->createRaceCode('foo'),
            $subRaceCode = $this->createSubRaceCode('bar'),
            $remarkableSenseCode = $this->createRemarkableSenseCode('baz'),
            $this->createTables($raceCode, $subRaceCode, 'qux')
        );
        self::assertSame(0, $zeroBonusFromUsedRemarkableSense->getValue());
        self::assertSame('0', (string)$zeroBonusFromUsedRemarkableSense);

        $aBonusFromUsedRemarkableSense = new BonusFromUsedRemarkableSense(
            $raceCode = $this->createRaceCode('foo'),
            $subRaceCode = $this->createSubRaceCode('bar'),
            $remarkableSenseCode = $this->createRemarkableSenseCode('baz'),
            $this->createTables($raceCode, $subRaceCode, 'baz')
        );
        self::assertSame(1, $aBonusFromUsedRemarkableSense->getValue());
        self::assertSame('1', (string)$aBonusFromUsedRemarkableSense);
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
     * @param $value
     * @return \Mockery\MockInterface|RemarkableSenseCode
     */
    private function createRemarkableSenseCode($value)
    {
        $remarkableSenseCode = $this->mockery(RemarkableSenseCode::class);
        $remarkableSenseCode->shouldReceive('getValue')
            ->andReturn($value);

        return $remarkableSenseCode;
    }

    /**
     * @param RaceCode $raceCode
     * @param SubRaceCode $subRaceCode
     * @param $remarkableSense
     * @return \Mockery\MockInterface|Tables
     */
    private function createTables(RaceCode $raceCode, SubRaceCode $subRaceCode, $remarkableSense)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getRacesTable')
            ->andReturn($racesTable = $this->mockery(RacesTable::class));
        $racesTable->shouldReceive('getRemarkableSense')
            ->with($raceCode, $subRaceCode)
            ->andReturn($remarkableSense);

        return $tables;
    }
}