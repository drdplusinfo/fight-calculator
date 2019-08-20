<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\CalculatorSkeleton\History;
use DrdPlus\CalculatorSkeleton\Memory;
use Mockery\MockInterface;

/**
 * @backupGlobals enabled
 */
class CalculatorApplicationTest extends \DrdPlus\Tests\CalculatorSkeleton\CalculatorApplicationTest
{
    use Partials\AttackCalculatorTestTrait;

    /**
     * @return Memory|MockInterface
     */
    protected function createMemoryForHistoryDeletion(): Memory
    {
        $memory = parent::createMemoryForHistoryDeletion();
        $memory->shouldReceive('getValue')
            ->andReturnNull();
        return $memory;
    }

    /**
     * @return History|MockInterface
     */
    protected function createHistoryForHistoryDeletion(): History
    {
        $history = parent::createHistoryForHistoryDeletion();
        $history->shouldReceive('getValue')
            ->andReturnNull();
        return $history;
    }

}