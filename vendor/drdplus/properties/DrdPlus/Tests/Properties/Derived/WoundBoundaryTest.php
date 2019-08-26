<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Properties\Derived\Toughness;
use DrdPlus\Properties\Derived\WoundBoundary;
use DrdPlus\Tables\Measurements\Wounds\Wounds;
use DrdPlus\Tables\Measurements\Wounds\WoundsBonus;
use DrdPlus\Tables\Measurements\Wounds\WoundsTable;
use DrdPlus\Tables\Tables;
use Mockery\MockInterface;

class WoundBoundaryTest extends AbstractDerivedPropertyTest
{
    protected function createIt(int $value): AbstractDerivedProperty
    {
        return WoundBoundary::getIt($toughness = $this->createToughness(), $this->createTables($value));
    }

    /**
     * @return Toughness|MockInterface
     */
    private function createToughness(): Toughness
    {
        $toughness = $this->mockery(Toughness::class);
        $toughness->shouldReceive('getValue')
            ->andReturn(0);
        return $toughness;
    }

    /**
     * @param int $woundsValue
     * @return Tables|MockInterface
     */
    private function createTables(int $woundsValue): Tables
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getWoundsTable')
            ->andReturn($woundsTable = $this->mockery(WoundsTable::class));
        $wounds = $this->mockery(Wounds::class);
        $wounds->shouldReceive('getValue')
            ->andReturn($woundsValue);
        $woundsTable->shouldReceive('toWounds')
            ->with($this->type(WoundsBonus::class))
            ->andReturnUsing(function (WoundsBonus $woundsBonus) use ($wounds) {
                self::assertSame(10, $woundsBonus->getValue());
                return $wounds;
            });
        return $tables;
    }
}