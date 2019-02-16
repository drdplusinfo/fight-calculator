<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Lighting;

use DrdPlus\Codes\Units\DistanceUnitCode;
use DrdPlus\Lighting\Opacity;
use DrdPlus\Tables\Measurements\Amount\Amount;
use DrdPlus\Tables\Measurements\Amount\AmountBonus;
use DrdPlus\Tables\Measurements\Amount\AmountTable;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Measurements\Distance\DistanceTable;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerObject;
use Granam\Tests\Tools\TestWithMockery;

class OpacityTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideDensityDistanceAndExpectedOpacity
     * @param int $density
     * @param int $distanceInMeters
     * @param int $expectedOpacity
     */
    public function I_can_get_opacity_from_barrier_density($density, $distanceInMeters, $expectedOpacity)
    {
        $opacity = Opacity::createFromBarrierDensity(
            new IntegerObject($density),
            new Distance($distanceInMeters, DistanceUnitCode::METER, new DistanceTable()),
            Tables::getIt()
        );
        self::assertSame($expectedOpacity, $opacity->getValue());
        self::assertSame((string)$expectedOpacity, (string)$opacity);
    }

    public function provideDensityDistanceAndExpectedOpacity()
    {
        return [
            [10, 3, 10], // note: there is a mistake in PPH on page 129, left column - distance bonus for 3 meters is probably taken lower (2) instead of higher (3)
            [10, 2, 6],
            [1, 1, 1],
            [-5, 900, 500],
        ];
    }

    /**
     * @test
     */
    public function I_can_get_zero_opacity_as_transparent()
    {
        $transparentOpacity = Opacity::createTransparent();
        self::assertSame(0, $transparentOpacity->getValue());
    }

    /**
     * @test
     */
    public function I_can_get_transparent_by_chance_on_small_distance_and_density()
    {
        for ($attempt = 1; $attempt < 100; $attempt++) {
            $opacity = Opacity::createFromBarrierDensity(
                new IntegerObject(-10),
                new Distance(1, DistanceUnitCode::METER, new DistanceTable()),
                Tables::getIt()
            );
            if ($opacity->getValue() === 0) {
                self::assertSame(0, $opacity->getValue());

                return;
            }
        }
        self::fail('Hundred of attempts was not enough to get transparent on distance of 1 meter and density -10');
    }

    /**
     * @test
     */
    public function I_get_transparent_on_small_density_and_distance()
    {
        $opacity = Opacity::createFromBarrierDensity(
            new IntegerObject(-80),
            new Distance(10, DistanceUnitCode::METER, new DistanceTable()),
            Tables::getIt()
        );
        self::assertSame(0, $opacity->getValue());
        $opacity = Opacity::createFromBarrierDensity(
            new IntegerObject(-21),
            new Distance(0.1, DistanceUnitCode::METER, new DistanceTable()),
            Tables::getIt()
        );
        self::assertSame(0, $opacity->getValue());
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_an_item_visibility()
    {
        $transparentOpacity = Opacity::createTransparent();
        self::assertSame(0, $transparentOpacity->getVisibilityMalus());

        $negativeOpacity = Opacity::createFromBarrierDensity(
            new IntegerObject(123),
            new Distance(1, DistanceUnitCode::METER, new DistanceTable()),
            $this->createTablesWithAmountTable(-1)
        );
        self::assertLessThan(0, $negativeOpacity->getValue());
        self::assertSame(0, $negativeOpacity->getVisibilityMalus(), 'Zero malus expected for negative opacity');

        $positiveOpacity = Opacity::createFromBarrierDensity(
            new IntegerObject(10),
            new Distance(5, DistanceUnitCode::METER, new DistanceTable()),
            Tables::getIt()
        );
        self::assertGreaterThan(0, $positiveOpacity->getValue());
        self::assertSame(-16, $positiveOpacity->getVisibilityMalus());
    }

    /**
     * @param $bonusToValue
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithAmountTable($bonusToValue)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getAmountTable')
            ->andReturn($cheatingAmountTable = $this->mockery(AmountTable::class));
        $cheatingAmountTable->shouldReceive('toAmount')
            ->with($this->type(AmountBonus::class))
            ->andReturn(new Amount($bonusToValue, Amount::AMOUNT, new AmountTable()));

        return $tables;
    }
}