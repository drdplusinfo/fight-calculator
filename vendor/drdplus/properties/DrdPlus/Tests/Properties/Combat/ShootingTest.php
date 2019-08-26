<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat;

use DrdPlus\Properties\Combat\Shooting;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Tests\Properties\Combat\Partials\CombatCharacteristicTest;

class ShootingTest extends CombatCharacteristicTest
{
    /**
     * @test
     */
    public function I_can_get_property_easily()
    {
        $shooting = Shooting::getIt($this->createKnack(5));
        self::assertInstanceOf(Shooting::class, $shooting);
        self::assertSame(2, $shooting->getValue());
    }

    /**
     * @return Shooting
     */
    protected function createSut()
    {
        return Shooting::getIt($this->createKnack(123));
    }

    /**
     * @test
     * @dataProvider getShootingByKnack
     * @param $knack
     * @param $shootingValue
     */
    public function I_can_get_shooting($knack, $shootingValue)
    {
        $shooting = Shooting::getIt($this->createKnack($knack));
        self::assertSame($shootingValue, $shooting->getValue());
    }

    public function getShootingByKnack()
    {
        return [
            [-2, -1],
            [-1, -1],
            [0, 0],
            [1, 0],
            [2, 1],
            [3, 1],
            [5, 2],
            [11, 5],
        ];
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Knack
     */
    private function createKnack($value)
    {
        $knack = \Mockery::mock(Knack::class);
        $knack->shouldReceive('getValue')
            ->andReturn($value);

        return $knack;
    }
}