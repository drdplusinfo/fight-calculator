<?php declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\QualityAndSuccess;

use Granam\DiceRolls\Roll;
use DrdPlus\RollsOn\QualityAndSuccess\ExtendedRollOnSuccess;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnKnowledge;
use DrdPlus\RollsOn\Traps\ShortRollOnIntelligence;
use Granam\Tests\Tools\TestWithMockery;

class RollOnKnowledgeTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideRollAndDifficultyAndExpectedResult
     * @param ShortRollOnIntelligence $shortRollOnIntelligence
     * @param int $difficulty
     * @param bool $isSuccess
     * @param string $expectedResult
     */
    public function I_can_fail_and_succeed(ShortRollOnIntelligence $shortRollOnIntelligence, $difficulty, $isSuccess, $expectedResult)
    {
        $rollOnKnowledge = new RollOnKnowledge($shortRollOnIntelligence, $difficulty);
        self::assertInstanceOf(ExtendedRollOnSuccess::class, $rollOnKnowledge);
        self::assertSame($isSuccess, $rollOnKnowledge->isSuccess());
        self::assertSame($expectedResult, $rollOnKnowledge->getResult());
    }

    public function provideRollAndDifficultyAndExpectedResult()
    {
        return [
            [$this->createShortRollOnIntelligence(6), 10, false, 'fatal_failure'],
            [$this->createShortRollOnIntelligence(7), 10, true, 'does_not_know_answer'],
            [$this->createShortRollOnIntelligence(9), 10, true, 'does_not_know_answer'],
            [$this->createShortRollOnIntelligence(10), 10, true, 'knows_answer'],
            [$this->createShortRollOnIntelligence(12), 10, true, 'knows_answer'],
            [$this->createShortRollOnIntelligence(13), 10, true, 'complete_success'],
        ];
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|ShortRollOnIntelligence
     */
    private function createShortRollOnIntelligence($value)
    {
        $rollOnIntelligence = $this->mockery(ShortRollOnIntelligence::class);
        $rollOnIntelligence->shouldReceive('getValue')
            ->andReturn($value);
        $rollOnIntelligence->shouldReceive('getPreconditionsSum')
            ->andReturn(123);
        $rollOnIntelligence->shouldReceive('getRoll')
            ->andReturn($roll = $this->mockery(Roll::class));
        $roll->shouldReceive('getValue')
            ->andReturn(456);
        $roll->shouldReceive('getRolledNumbers')
            ->andReturn([]);

        return $rollOnIntelligence;
    }
}