<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Lighting;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Lighting\EyesAdaptation;
use DrdPlus\Lighting\LightingQuality;
use DrdPlus\Lighting\Opacity;
use DrdPlus\Lighting\Partials\WithInsufficientLightingBonus;
use DrdPlus\Lighting\UnsuitableLightingQualityMalus;
use DrdPlus\Tables\Races\RacesTable;
use DrdPlus\Tables\Races\SightRangesTable;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class UnsuitableLightingQualityMalusTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideLightingQualityAndExpectedMalus
     * @param int $lightingQualityValue
     * @param int $barrierOpacityValue
     * @param string $raceValue
     * @param int $duskSightBonus
     * @param bool $hasInfravision
     * @param bool $situationAllowsUseOfInfravision
     * @param int $expectedMalus
     */
    public function I_get_malus_from_unsuitable_lighting(
        $lightingQualityValue,
        $barrierOpacityValue,
        $raceValue,
        $duskSightBonus,
        $hasInfravision,
        $situationAllowsUseOfInfravision,
        $expectedMalus
    )
    {
        $unsuitableLightingQualityMalus = UnsuitableLightingQualityMalus::createWithSimplifiedRules(
            new LightingQuality($lightingQualityValue),
            $this->createOpacity($barrierOpacityValue),
            $this->createDuskSight($duskSightBonus),
            $raceCode = RaceCode::getIt($raceValue),
            $subRaceCode = $this->createSubRaceCode(),
            $this->createTablesWithRacesTable($raceCode, $subRaceCode, $hasInfravision),
            $situationAllowsUseOfInfravision
        );
        self::assertSame($expectedMalus, $unsuitableLightingQualityMalus->getValue());
        self::assertSame((string)$expectedMalus, (string)$unsuitableLightingQualityMalus);
    }

    public function provideLightingQualityAndExpectedMalus()
    {
        // lightingQuality, barrierOpacity, race, duskSightBonus, infravisionCanBeUsed, expectedMalus
        // note: orcs and dwarfs have +4 bonus in darkness, krolls +2 but orcs have -2 malus on bright light
        return [
            [0, -200, RaceCode::HUMAN, 0, false, true, 0],
            [0, 0, RaceCode::HUMAN, 0, false, true, 0],
            [-10, 0, RaceCode::ELF, 0, false, true, 0],
            [-11, 0, RaceCode::HOBBIT, 0, false, true, -1],
            [-11, 0, RaceCode::HOBBIT, 1, false, true, 0],
            [-14, 0, RaceCode::HUMAN, 0, false, true, -1],
            [-15, 0, RaceCode::HUMAN, 0, false, true, -2],
            [-54, 20, RaceCode::ELF, 0, false, true, -7],
            [-54, 0, RaceCode::KROLL, 0, false, true, -3],
            [-54, 0, RaceCode::ORC, 0, true, true, -1],
            [-54, 0, RaceCode::DWARF, 0, true, true, -1],
            [-100, 0, RaceCode::HOBBIT, 0, false, true, -10],
            [-200, 0, RaceCode::HOBBIT, 0, false, false, -20],
            [-200, 0, RaceCode::ORC, 3, true, true, -10],
            [-200, 0, RaceCode::ORC, 3, true, false, -13],
            [-999, 0, RaceCode::DWARF, 90, true, true, -3],
            [-999, 0, RaceCode::DWARF, 90, true, false, -6],
            [-999, 0, RaceCode::DWARF, 0, true, true, -20 /* maximum is -20 */],
            [60, 0, RaceCode::KROLL, 0, false, true, 0],
            [59, 0, RaceCode::ORC, 0, true, true, 0],
            [60, 0, RaceCode::ORC, 0, true, true, -2],
            [61, 1, RaceCode::ORC, 0, true, true, -2],
            [61, 2, RaceCode::ORC, 0, true, true, 0],
            [-999, 0, RaceCode::DWARF, 100000, true, true, 0 /* malus can not turns to bonus */],
        ];
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|Opacity
     */
    private function createOpacity($value)
    {
        $opacity = $this->mockery(Opacity::class);
        $opacity->shouldReceive('getValue')
            ->andReturn($value);

        return $opacity;
    }

    /**
     * @param int $bonus
     * @return \Mockery\MockInterface|WithInsufficientLightingBonus
     */
    private function createDuskSight($bonus)
    {
        $duskSight = $this->mockery(WithInsufficientLightingBonus::class);
        $duskSight->shouldReceive('getInsufficientLightingBonus')
            ->andReturn($bonus);

        return $duskSight;
    }

    /**
     * @return \Mockery\MockInterface|SubRaceCode
     */
    private function createSubRaceCode()
    {
        return $this->mockery(SubRaceCode::class);
    }

    /**
     * @param RaceCode $raceCode
     * @param SubRaceCode $subRaceCode
     * @param $hasInfravision
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithRacesTable(RaceCode $raceCode, SubRaceCode $subRaceCode, $hasInfravision)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getRacesTable')
            ->andReturn($racesTable = $this->mockery(RacesTable::class));
        $racesTable->shouldReceive('hasInfravision')
            ->with($raceCode, $subRaceCode)
            ->andReturn($hasInfravision);

        return $tables;
    }

    /**
     * @test
     * @dataProvider provideEyesAdaptationAndExpectedMalus
     * @param int $eyesAdaptationValue
     * @param int $lightingQualityValue
     * @param int $raceAdaptability
     * @param int $barrierOpacityValue
     * @param string $raceValue
     * @param int $duskSightBonus
     * @param bool $hasInfravision
     * @param bool $situationAllowsUseOfInfravision
     * @param int $expectedMalus
     */
    public function I_get_malus_when_eyes_are_not_yet_adapted(
        $eyesAdaptationValue,
        $lightingQualityValue,
        $raceAdaptability,
        $barrierOpacityValue,
        $raceValue,
        $duskSightBonus,
        $hasInfravision,
        $situationAllowsUseOfInfravision,
        $expectedMalus
    )
    {
        $unsuitableLightingQualityMalus = UnsuitableLightingQualityMalus::createWithEyesAdaptation(
            $this->createEyesAdaptation($eyesAdaptationValue),
            new LightingQuality($lightingQualityValue),
            $this->createOpacity($barrierOpacityValue),
            $this->createDuskSight($duskSightBonus),
            $raceCode = RaceCode::getIt($raceValue),
            $subRaceCode = $this->createSubRaceCode(),
            $this->createTables($raceCode, $raceAdaptability, $subRaceCode, $hasInfravision),
            $situationAllowsUseOfInfravision
        );
        self::assertSame($expectedMalus, $unsuitableLightingQualityMalus->getValue());
        self::assertSame((string)$expectedMalus, (string)$unsuitableLightingQualityMalus);
    }

    public function provideEyesAdaptationAndExpectedMalus()
    {
        $sightRangesTable = new SightRangesTable();
        // eyesAdaptation, lightingQuality, raceAdaptability, barrierOpacity, race, duskSightBonus,
        // hasInfravision, situationAllowsUseOfInfravision, expectedMalus
        return [
            /**
             * For absolute darkness example see PPH page 130 right column, @link https://pph.drdplus.jaroslavtyc.com/#postihy_pri_uplne_tme
             */
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::HUMAN)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::HUMAN)), 0, RaceCode::HUMAN, 0, false, false, -20],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::HOBBIT)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::HOBBIT)), 0, RaceCode::HOBBIT, 0, false, false, -20],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::ELF)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::ELF)), 0, RaceCode::ELF, 0, false, false, -20],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::KROLL)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::KROLL)), 0, RaceCode::KROLL, 0, false, false, -20],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::DWARF)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::DWARF)), 0, RaceCode::DWARF, 0, true, false, -20],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::ORC)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::ORC)), 0, RaceCode::ORC, 0, true, false, -20],
            // with usable infravision
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::HUMAN)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::HUMAN)), 0, RaceCode::HUMAN, 0, false, true, -20],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::HOBBIT)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::HOBBIT)), 0, RaceCode::HOBBIT, 0, false, true, -20],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::ELF)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::ELF)), 0, RaceCode::ELF, 0, false, true, -20],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::KROLL)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::KROLL)), 0, RaceCode::KROLL, 0, false, true, -20],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::DWARF)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::DWARF)), 0, RaceCode::DWARF, 0, true, true, -17],
            [$sightRangesTable->getMinimalLighting(RaceCode::getIt(RaceCode::ORC)), -200, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::ORC)), 0, RaceCode::ORC, 0, true, true, -17],
            /**
             * For contrast 80 of bright light example see PPH page 130 right column, @link https://pph.drdplus.jaroslavtyc.com/#postihy_pri_extremne_ostrem_magickem_svetle
             * Note: there is an error in the example - to fit maluses listed there the light quality has to be 79 instead of 80.
             */
            [$sightRangesTable->getMaximalLighting(RaceCode::getIt(RaceCode::HUMAN)), 79, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::HUMAN)), 0, RaceCode::HUMAN, 0, false, true, -2],
            [$sightRangesTable->getMaximalLighting(RaceCode::getIt(RaceCode::HOBBIT)), 79, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::HOBBIT)), 0, RaceCode::HOBBIT, 0, false, true, -2],
            [$sightRangesTable->getMaximalLighting(RaceCode::getIt(RaceCode::ELF)), 79, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::ELF)), 0, RaceCode::ELF, 0, false, true, -3],
            [$sightRangesTable->getMaximalLighting(RaceCode::getIt(RaceCode::KROLL)), 79, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::KROLL)), 0, RaceCode::KROLL, 0, false, true, -3],
            [$sightRangesTable->getMaximalLighting(RaceCode::getIt(RaceCode::DWARF)), 79, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::DWARF)), 0, RaceCode::DWARF, 0, true, true, -4],
            [$sightRangesTable->getMaximalLighting(RaceCode::getIt(RaceCode::ORC)), 79, $sightRangesTable->getAdaptability(RaceCode::getIt(RaceCode::ORC)), 0, RaceCode::ORC, 0, true, true, -5],
            [10, 20, 2, 50, RaceCode::ELF, 12, false, false, -8],
        ];
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|EyesAdaptation
     */
    private function createEyesAdaptation($value)
    {
        $eyesAdaptation = $this->mockery(EyesAdaptation::class);
        $eyesAdaptation->shouldReceive('getValue')
            ->andReturn($value);

        return $eyesAdaptation;
    }

    /**
     * @param RaceCode $raceCode
     * @param int $raceAdaptability
     * @param SubRaceCode $subRaceCode
     * @param $hasInfravision
     * @return \Mockery\MockInterface|Tables
     */
    private function createTables(RaceCode $raceCode, $raceAdaptability, SubRaceCode $subRaceCode, $hasInfravision)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getSightRangesTable')
            ->andReturn($sightRangesTable = $this->mockery(SightRangesTable::class));
        $sightRangesTable->shouldReceive('getAdaptability')
            ->with($raceCode)
            ->andReturn($raceAdaptability);
        $tables->shouldReceive('getRacesTable')
            ->andReturn($racesTable = $this->mockery(RacesTable::class));
        $racesTable->shouldReceive('hasInfravision')
            ->with($raceCode, $subRaceCode)
            ->andReturn($hasInfravision);

        return $tables;
    }
}