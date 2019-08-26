<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\Properties\Derived\MovementSpeed;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Properties\Derived\Speed;
use Mockery\MockInterface;

class MovementSpeedTest extends AbstractDerivedPropertyTest
{
    protected function createIt(int $value): AbstractDerivedProperty
    {
        return MovementSpeed::getIt($this->createSpeed($value * 2));
    }

    /**
     * @param int $value
     * @return Speed|MockInterface
     */
    private function createSpeed(int $value): Speed
    {
        $speed = $this->mockery(Speed::class);
        $speed->shouldReceive('getValue')
            ->andReturn($value);
        return $speed;
    }
}