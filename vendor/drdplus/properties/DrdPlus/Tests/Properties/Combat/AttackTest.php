<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat;

use DrdPlus\Properties\Combat\Attack;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\Tests\Properties\Combat\Partials\CombatCharacteristicTest;

class AttackTest extends CombatCharacteristicTest
{
    protected function createSut()
    {
        return Attack::getIt($this->createAgility(123));
    }

    /**
     * @test
     */
    public function I_can_get_property_easily(): void
    {
        for ($value = -5; $value < 10; $value++) {
            $attack = Attack::getIt($this->createAgility($value));
            self::assertSame((int)floor($value / 2), $attack->getValue());
        }
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Agility
     */
    private function createAgility(int $value)
    {
        $agility = \Mockery::mock(Agility::class);
        $agility->shouldReceive('getValue')
            ->andReturn($value);

        return $agility;
    }
}