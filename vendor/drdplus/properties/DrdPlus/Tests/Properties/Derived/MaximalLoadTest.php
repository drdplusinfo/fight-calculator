<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\BaseProperties\Strength;
use DrdPlus\Properties\Derived\MaximalLoad;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Tables\Properties\AthleticsInterface;
use Granam\Integer\PositiveIntegerObject;
use Mockery\MockInterface;

class MaximalLoadTest extends AbstractDerivedPropertyTest
{
    protected function createIt(int $value): AbstractDerivedProperty
    {
        return MaximalLoad::getIt(Strength::getIt($value - 21), $this->createZeroAthletics());
    }

    /**
     * @return AthleticsInterface|MockInterface
     */
    private function createZeroAthletics(): AthleticsInterface
    {
        $athletics = $this->mockery(AthleticsInterface::class);
        $athletics->shouldReceive('getAthleticsBonus')
            ->andReturn(new PositiveIntegerObject(0));
        return $athletics;
    }
}