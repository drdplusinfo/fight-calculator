<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Lighting;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Lighting\EyesAdaptation;
use DrdPlus\Lighting\LightingQuality;
use DrdPlus\Lighting\Partials\LightingQualityInterface;
use DrdPlus\Tables\Races\SightRangesTable;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveIntegerObject;
use Granam\Tests\Tools\TestWithMockery;

class EyesAdaptationTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideValuesForEyeAdaptation
     * @param int $previousLightingQuality
     * @param int $currentLightingQuality
     * @param int $maximalLightingForRace
     * @param int $minimalLightingForRace
     * @param int $roundsOfAdaptation
     * @param int $expectedEyeAdaptation
     */
    public function I_can_use_it(
        $previousLightingQuality,
        $currentLightingQuality,
        $maximalLightingForRace,
        $minimalLightingForRace,
        $roundsOfAdaptation,
        $expectedEyeAdaptation
    )
    {
        $eyeAdaptation = new EyesAdaptation(
            $this->createLightingQuality($previousLightingQuality),
            $this->createLightingQuality($currentLightingQuality),
            $raceCode = $this->createRaceCode(),
            $this->createTablesWithSightRangesTable($maximalLightingForRace, $minimalLightingForRace, $raceCode),
            new PositiveIntegerObject($roundsOfAdaptation)
        );
        self::assertSame($expectedEyeAdaptation, $eyeAdaptation->getValue());
        self::assertSame((string)$expectedEyeAdaptation, (string)$eyeAdaptation);
        self::assertInstanceOf(LightingQualityInterface::class, $eyeAdaptation);
    }

    public function provideValuesForEyeAdaptation()
    {
        return [
            [0, 0, 0, 0, 999, 0],
            [-100, 100, 32, 0, 999, 32], // previous limited by minimal, current by maximal; from dark ro light
            [100, -100, 32, 0, 999, -32], // previous limited by maximal, current by minimal; from light to dark
            [100, -100, 500, -500, 22, -22], // previous limited by maximal, current by minimal; from light to dark without enough time
            [-100, 100, 500, -500, 29, 2], // previous limited by maximal, current by minimal; from light to dark (ten times slower) without enough time
        ];
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|LightingQuality
     */
    private function createLightingQuality($value)
    {
        $lightingQuality = $this->mockery(LightingQuality::class);
        $lightingQuality->shouldReceive('getValue')
            ->andReturn($value);

        return $lightingQuality;
    }

    /**
     * @return \Mockery\MockInterface|RaceCode
     */
    private function createRaceCode()
    {
        return $this->mockery(RaceCode::class);
    }

    /**
     * @param $maximalLightingForRace
     * @param $minimalLightingForRace
     * @param RaceCode $raceCode
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithSightRangesTable(
        $maximalLightingForRace,
        $minimalLightingForRace,
        RaceCode $raceCode
    )
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getSightRangesTable')
            ->andReturn($sightRangesTable = $this->mockery(SightRangesTable::class));
        $sightRangesTable->shouldReceive('getMaximalLighting')
            ->with($raceCode)
            ->andReturn($maximalLightingForRace);
        $sightRangesTable->shouldReceive('getMinimalLighting')
            ->with($raceCode)
            ->andReturn($minimalLightingForRace);

        return $tables;
    }
}