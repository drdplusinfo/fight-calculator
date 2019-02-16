<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Background\BackgroundParts;

use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Codes\History\AncestryCode;
use DrdPlus\Tables\History\AncestryTable;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Background\BackgroundParts\Partials\AbstractBackgroundAdvantageTest;
use Granam\Integer\PositiveInteger;
use Granam\Integer\PositiveIntegerObject;
use Mockery\MockInterface;

class AncestryTest extends AbstractBackgroundAdvantageTest
{
    protected function createSutToTestSpentBackgroundPoints(PositiveInteger $spentBackgroundPoints): Ancestry
    {
        return Ancestry::getIt($spentBackgroundPoints, Tables::getIt());
    }

    /**
     * @test
     */
    public function I_can_get_ancestry_code(): void
    {
        $ancestryCode = $this->createAncestryCode();
        $tables = $this->createTablesWithAncestryTable(
            function (PositiveInteger $positiveInteger) use ($ancestryCode) {
                self::assertSame(6, $positiveInteger->getValue());

                return $ancestryCode;
            }
        );

        $ancestry = Ancestry::getIt(new PositiveIntegerObject(6), $tables);
        self::assertSame(6, $ancestry->getValue());
        /** @var AncestryTable $ancestryTable */
        self::assertSame($ancestryCode, $ancestry->getAncestryCode($tables));
    }

    /**
     * @param \Closure $getAncestryCode
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithAncestryTable(\Closure $getAncestryCode): Tables
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getAncestryTable')
            ->andReturn($ancestryTable = $this->mockery(AncestryTable::class));
        $ancestryTable->shouldReceive('getAncestryCodeByBackgroundPoints')
            ->atLeast()->once()
            ->with($this->type(PositiveInteger::class))
            ->andReturnUsing($getAncestryCode);

        return $tables;
    }

    /**
     * @return AncestryCode|MockInterface
     */
    private function createAncestryCode(): AncestryCode
    {
        return $this->mockery(AncestryCode::class);
    }
}