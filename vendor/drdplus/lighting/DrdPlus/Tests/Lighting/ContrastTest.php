<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Lighting;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Lighting\Contrast;
use DrdPlus\Lighting\EyesAdaptation;
use DrdPlus\Lighting\LightingQuality;
use DrdPlus\Tables\Races\SightRangesTable;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class ContrastTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider providePreviousAndCurrentLightIntensity
     * @param int $previousLightIntensityValue
     * @param int $currentLightIntensityValue
     * @param int $expectedContrast
     */
    public function I_can_create_it_by_simplified_rules(
        int $previousLightIntensityValue,
        int $currentLightIntensityValue,
        int $expectedContrast
    ): void
    {
        $contrast = Contrast::createBySimplifiedRules(
            new LightingQuality($previousLightIntensityValue),
            new LightingQuality($currentLightIntensityValue)
        );
        self::assertSame($expectedContrast, $contrast->getValue());
        $asString = (string)$expectedContrast;
        if ($contrast->isFromLightToDark()) {
            $asString .= ' (to dark)';
        }
        if ($contrast->isFromDarkToLight()) {
            $asString .= ' (to light)';
        }
        self::assertSame($asString, (string)$contrast);
        self::assertSame($previousLightIntensityValue > $currentLightIntensityValue, $contrast->isFromLightToDark());
        self::assertSame($previousLightIntensityValue < $currentLightIntensityValue, $contrast->isFromDarkToLight());
    }

    public function providePreviousAndCurrentLightIntensity(): array
    {
        return [
            [-123, 345, 47],
            [678, 1, 68],
            [-5, -5, 0], // same values = no diff
        ];
    }

    /**
     * @test
     * @dataProvider provideAdaptationCurrentLightIntensityAndAdaptability
     * @param int $eyeAdaptationValue
     * @param int $currentLightIntensityValue
     * @param int $adaptability
     * @param int $expectedContrast
     */
    public function I_can_create_it_by_extended_rules(
        int $eyeAdaptationValue,
        int $currentLightIntensityValue,
        int $adaptability,
        int $expectedContrast
    ): void
    {
        $contrast = Contrast::createByExtendedRules(
            $this->createEyeAdaptation($eyeAdaptationValue),
            new LightingQuality($currentLightIntensityValue),
            $raceCode = $this->createRaceCode(),
            $this->createTablesWithSightRangesTable($raceCode, $adaptability)
        );
        self::assertSame($expectedContrast, $contrast->getValue());
        $asString = (string)$expectedContrast;
        if ($contrast->isFromLightToDark()) {
            $asString .= ' (to dark)';
        }
        if ($contrast->isFromDarkToLight()) {
            $asString .= ' (to light)';
        }
        self::assertSame($asString, (string)$contrast);
        self::assertSame($eyeAdaptationValue > $currentLightIntensityValue, $contrast->isFromLightToDark());
        self::assertSame($eyeAdaptationValue < $currentLightIntensityValue, $contrast->isFromDarkToLight());
    }

    public function provideAdaptationCurrentLightIntensityAndAdaptability(): array
    {
        return [
            [-123, 345, 10, 47],
            [-123, 345, 5, 94],
            [678, 1, 10, 68],
            [678, 1, 11, 62],
            [55, 55, 123, 0], // same values = no diff
        ];
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|EyesAdaptation
     */
    private function createEyeAdaptation($value)
    {
        $eyeAdaptation = $this->mockery(EyesAdaptation::class);
        $eyeAdaptation->expects('getValue')
            ->andReturn($value);

        return $eyeAdaptation;
    }

    /**
     * @return \Mockery\MockInterface|RaceCode
     */
    private function createRaceCode()
    {
        return $this->mockery(RaceCode::class);
    }

    /**
     * @param RaceCode $raceCode
     * @param int $adaptability
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithSightRangesTable(RaceCode $raceCode, int $adaptability)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getSightRangesTable')
            ->andReturn($sightRangesTable = $this->mockery(SightRangesTable::class));
        $sightRangesTable->expects('getAdaptability')
            ->with($raceCode)
            ->andReturn($adaptability);

        return $tables;
    }
}