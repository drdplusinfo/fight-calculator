<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat\Partials;

use DrdPlus\Properties\Combat\EncounterRange;
use DrdPlus\Properties\Combat\MaximalRange;
use DrdPlus\Properties\Combat\Partials\AbstractRange;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Measurements\Distance\DistanceBonus;
use DrdPlus\Tables\Measurements\Distance\DistanceTable;
use DrdPlus\Tables\Tables;

abstract class AbstractRangeTest extends CharacteristicForGameTest
{
    /**
     * @param int $value
     * @return AbstractRange|EncounterRange|MaximalRange
     */
    protected function createSut($value = 123)
    {
        return $this->createRangeSut($value);
    }

    /**
     * @param $value
     * @return AbstractRange|EncounterRange|MaximalRange
     */
    abstract protected function createRangeSut($value);

    /**
     * @return array|string[]
     */
    protected function getExpectedInitialChangeBy(): array
    {
        return [
            'name' => 'create range sut',
            'with' => '123',
        ];
    }

    /**
     * @test
     * @dataProvider provideSomeDistanceBonus
     * @param int $distanceBonusValue
     */
    public function I_can_get_its_value_in_meters(int $distanceBonusValue)
    {
        /** @var EncounterRange $range */
        $range = $this->createSut($distanceBonusValue);
        $distanceValue = 456;
        $tables = $this->createTablesWithDistanceTable(
            function (DistanceBonus $distanceBonus) use ($distanceValue, $distanceBonusValue) {
                self::assertSame($distanceBonusValue, $distanceBonus->getValue());
                $distance = $this->mockery(Distance::class);
                $distance->shouldReceive('getMeters')
                    ->andReturn($distanceValue);

                return $distance;
            }
        );

        self::assertSame((float)$distanceValue, $range->getInMeters($tables));
    }

    public function provideSomeDistanceBonus()
    {
        return [
            [123],
            [-9], // distance bonus -9 = 1 meter
        ];
    }

    /**
     * @param \Closure $toDistance
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithDistanceTable(\Closure $toDistance)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getDistanceTable')
            ->andReturn($distanceTable = $this->mockery(DistanceTable::class));
        $distanceTable->shouldReceive('toDistance')
            ->with(\Mockery::type(DistanceBonus::class))
            ->andReturnUsing($toDistance);

        return $tables;
    }
}