<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\AttackSkeleton\AttackRequest;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use Mockery\MockInterface;

class AttackRequestTest extends AbstractAttackTest
{
    /**
     * @test
     */
    public function I_can_get_scroll_from_top(): void
    {
        $frontendHelper = new AttackRequest($this->createCurrentValues(123456), $this->getBot(), $this->getEnvironment());
        self::assertSame(123456, $frontendHelper->getScrollFromTop());
    }

    /**
     * @param int|null $scrollFromTop
     * @return CurrentValues|MockInterface
     */
    private function createCurrentValues(int $scrollFromTop = null): CurrentValues
    {
        $currentValues = $this->mockery(CurrentValues::class);
        $currentValues->shouldReceive('getSelectedValue')
            ->with(AttackRequest::SCROLL_FROM_TOP)
            ->andReturn($scrollFromTop);

        return $currentValues;
    }
}