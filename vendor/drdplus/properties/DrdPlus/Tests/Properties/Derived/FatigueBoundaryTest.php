<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Derived\Endurance;
use DrdPlus\Properties\Derived\FatigueBoundary;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Tables\Measurements\Fatigue\Fatigue;
use DrdPlus\Tables\Measurements\Fatigue\FatigueBonus;
use DrdPlus\Tables\Measurements\Fatigue\FatigueTable;
use DrdPlus\Tables\Tables;
use Mockery\MockInterface;

class FatigueBoundaryTest extends AbstractDerivedPropertyTest
{
    protected function createIt(int $value): AbstractDerivedProperty
    {
        return FatigueBoundary::getIt(Endurance::getIt(Strength::getIt(123), Will::getIt(456)), $this->createTables($value));
    }

    /**
     * @param int $value
     * @return Tables|MockInterface
     */
    private function createTables(int $value): Tables
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getFatigueTable')
            ->andReturn($fatigueTable = $this->mockery(FatigueTable::class));
        $fatigueTable->shouldReceive('toFatigue')
            ->with($this->type(FatigueBonus::class))
            ->andReturn($fatigue = $this->mockery(Fatigue::class));
        $fatigue->shouldReceive('getValue')
            ->andReturn($value);
        return $tables;
    }
}