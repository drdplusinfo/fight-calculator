<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat;

use DrdPlus\Properties\Combat\Attack;
use DrdPlus\Properties\Combat\AttackNumber;
use DrdPlus\Properties\Combat\Partials\CharacteristicForGame;
use DrdPlus\Properties\Combat\Shooting;
use DrdPlus\Tests\Properties\Combat\Partials\CharacteristicForGameTest;

class AttackNumberTest extends CharacteristicForGameTest
{
    protected function createSut()
    {
        return AttackNumber::getItFromAttack($this->createAttack(123));
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Attack
     */
    private function createAttack($value)
    {
        $attack = $this->mockery(Attack::class);
        $attack->shouldReceive('getValue')
            ->andReturn($value);

        return $attack;
    }

    /**
     * @test
     */
    public function I_can_get_property_easily()
    {
        $this->I_can_create_it_from_attack();
        $this->I_can_create_it_from_shooting();
    }

    private function I_can_create_it_from_attack()
    {
        $attackNumber = AttackNumber::getItFromAttack($this->createAttack(567));
        self::assertInstanceOf(AttackNumber::class, $attackNumber);
        self::assertInstanceOf(CharacteristicForGame::class, $attackNumber);
        self::assertSame(567, $attackNumber->getValue());
    }

    private function I_can_create_it_from_shooting()
    {
        $attackNumber = AttackNumber::getItFromShooting($this->createShooting(890));
        self::assertInstanceOf(AttackNumber::class, $attackNumber);
        self::assertInstanceOf(CharacteristicForGame::class, $attackNumber);
        self::assertSame(890, $attackNumber->getValue());
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Shooting
     */
    private function createShooting($value)
    {
        $attack = $this->mockery(Shooting::class);
        $attack->shouldReceive('getValue')
            ->andReturn($value);

        return $attack;
    }

}