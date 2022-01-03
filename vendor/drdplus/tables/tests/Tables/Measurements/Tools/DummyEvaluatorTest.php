<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Tables\Measurements\Tools;

use DrdPlus\Tables\Measurements\Tools\DummyEvaluator;
use Granam\TestWithMockery\TestWithMockery;

class DummyEvaluatorTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_not_use_it_for_evaluation()
    {
        $this->expectException(\DrdPlus\Tables\Measurements\Exceptions\UnsupportedMethodCalled::class);
        $evaluator = new DummyEvaluator();
        $evaluator->evaluate(123);
    }
}
