<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Background;

use DrdPlus\Codes\History\FateCode;
use DrdPlus\Background\BackgroundPoints;
use DrdPlus\Tables\History\BackgroundPointsTable;
use DrdPlus\Tables\Tables;

class BackgroundPointsTest extends AbstractTestOfEnum
{
    /**
     * @test
     */
    public function I_can_get_background_points_by_fate(): void
    {
        $fateCode = $this->createFateCode();
        $tables = $this->createTablesWithBackgroundPointsTable($fateCode, 123);
        $backgroundPoints = BackgroundPoints::getIt($fateCode, $tables);
        self::assertSame(123, $backgroundPoints->getValue());
    }

    /**
     * @return \Mockery\MockInterface|FateCode
     */
    private function createFateCode(): FateCode
    {
        return $this->mockery(FateCode::class);
    }

    /**
     * @param FateCode $fateCode
     * @param int $backgroundPoints
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithBackgroundPointsTable(FateCode $fateCode, $backgroundPoints): Tables
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getBackgroundPointsTable')
            ->andReturn($backgroundPointsTable = $this->mockery(BackgroundPointsTable::class));
        $backgroundPointsTable->shouldReceive('getBackgroundPointsByPlayerDecision')
            ->with($fateCode)
            ->andReturn($backgroundPoints);

        return $tables;
    }
}