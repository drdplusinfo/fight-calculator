<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Lighting;

use DrdPlus\Lighting\Contrast;
use DrdPlus\Lighting\Glare;
use DrdPlus\RollsOn\Traps\RollOnSenses;
use Granam\Tests\Tools\TestWithMockery;

class GlareTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideContrastRollOnSensesAndMalus
     * @param int $contrastValue
     * @param bool $fromDarkToLight
     * @param int $rollOnSensesValue
     * @param bool $wasPrepared
     * @param int $expectedMalus
     */
    public function I_can_get_malus_from_glare($contrastValue, $fromDarkToLight, $rollOnSensesValue, $wasPrepared, $expectedMalus)
    {
        $glare = new Glare($this->createContrast($contrastValue, $fromDarkToLight), $this->createRollOnSenses($rollOnSensesValue), $wasPrepared);
        self::assertSame($expectedMalus, $glare->getMalus());
        self::assertSame($expectedMalus !== 0 && $fromDarkToLight, $glare->isShined());
        self::assertSame($expectedMalus !== 0 && !$fromDarkToLight, $glare->isBlinded());
    }

    public function provideContrastRollOnSensesAndMalus()
    {
        return [
            [123, true, 21, true, -110], // - (123 - 7) + 6
            [123, true, 21, false, -116], // - (123 - 7)
            [123, false, 985, true, -116], // - (123 - 1) + 6
            [123, false, 985, false, -122], // - (123 - 1)
            [-456, true, 654, true, 0],
            [0, true, 1, false, 0],
            [1, false, 35, true, 0],
            [2, false, 35, false, -1], // - (2 - 1)
            [2, false, 35, true, 0], // - (2 - 1) + 6
        ];
    }

    /**
     * @param int $value
     * @param bool $fromDarkToLight
     * @return \Mockery\MockInterface|Contrast
     */
    private function createContrast($value, $fromDarkToLight)
    {
        $contrast = $this->mockery(Contrast::class);
        $contrast->shouldReceive('getValue')
            ->andReturn($value);
        $contrast->shouldReceive('isFromDarkToLight')
            ->andReturn($fromDarkToLight);
        $contrast->shouldReceive('isFromLightToDark')
            ->andReturn(!$fromDarkToLight);

        return $contrast;
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|RollOnSenses
     */
    private function createRollOnSenses($value)
    {
        $contrast = $this->mockery(RollOnSenses::class);
        $contrast->shouldReceive('getValue')
            ->andReturn($value);

        return $contrast;
    }

    /**
     * Just an explicit test of a note about glare starting from lighting difference of twenty,
     * see PPH page 128 left column bottom, @link https://pph.drdplus.jaroslavtyc.com/#oslneni_az_pri_zmene_alespon
     * @test
     */
    public function I_am_not_affected_if_contrast_is_just_one()
    {
        $glare = new Glare(
            $this->createContrast(1, true /* from dark to light - does not affect anything here */),
            $this->createRollOnSenses(0 /* just lesser than contrast */),
            false /* not prepared */
        );
        self::assertSame(0, $glare->getMalus());
    }
}