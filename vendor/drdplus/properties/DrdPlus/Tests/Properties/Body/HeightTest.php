<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Body;

use DrdPlus\Codes\Units\DistanceUnitCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Measurements\Distance\DistanceBonus;
use DrdPlus\Tables\Measurements\Distance\DistanceTable;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\BaseProperties\Partials\PropertyTest;

class HeightTest extends PropertyTest
{
    use BodyPropertyTest;

    protected function getExpectedCodeClass(): string
    {
        return PropertyCode::class;
    }

    /**
     * @test
     */
    public function I_can_get_property_easily(): void
    {
        $tables = $this->createTablesWithDistanceTable(
            function (Distance $distance) {
                self::assertSame(DistanceUnitCode::DECIMETER, $distance->getUnit());
                self::assertSame(12.3, $distance->getValue());

                return $this->createDistanceBonus(456);
            }
        );
        $height = Height::getIt($this->createHeightInCm(123), $tables);
        self::assertSame(456, $height->getValue());
        self::assertSame('456', (string)$height);
        self::assertSame(PropertyCode::getIt($this->getExpectedPropertyCode()), $height->getCode());
    }

    /**
     * @param int $value
     * @return HeightInCm|\Mockery\MockInterface
     */
    protected function createValueForProperty($value)
    {
        return $this->createHeightInCm($value);
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|HeightInCm
     */
    private function createHeightInCm($value)
    {
        $heightInCm = $this->mockery(HeightInCm::class);
        $heightInCm->shouldReceive('getValue')
            ->andReturn($value);

        return $heightInCm;
    }

    /**
     * @param \Closure $toBonus
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithDistanceTable(\Closure $toBonus)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getDistanceTable')
            ->andReturn($distanceTable = $this->mockery(DistanceTable::class));
        $distanceTable->shouldReceive('toBonus')
            ->zeroOrMoreTimes()
            ->andReturnUsing($toBonus);

        return $tables;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|DistanceBonus
     */
    private function createDistanceBonus($value)
    {
        $distanceBonus = $this->mockery(DistanceBonus::class);
        $distanceBonus->shouldReceive('getValue')
            ->andReturn($value);

        return $distanceBonus;
    }

    /**
     * @test
     */
    public function I_can_get_height_in_cm(): void
    {
        $heightInCm = HeightInCm::getIt(123.456);
        $height = Height::getIt($heightInCm, Tables::getIt());
        self::assertSame($heightInCm, $height->getHeightInCm());
    }
}
