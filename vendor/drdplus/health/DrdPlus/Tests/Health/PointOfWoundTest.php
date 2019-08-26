<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health;

use DrdPlus\Health\PointOfWound;
use DrdPlus\Health\Wound;
use Granam\Tests\Tools\TestWithMockery;

class PointOfWoundTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it(): void
    {
        $pointOfWound = new PointOfWound($wound = $this->createWound());
        self::assertSame(1, $pointOfWound->getValue());
        self::assertSame($wound, $pointOfWound->getWound());
        self::assertSame('1', (string)$pointOfWound);
    }

    /**
     * @return \Mockery\MockInterface|Wound
     */
    private function createWound(): Wound
    {
        return $this->mockery(Wound::class);
    }
}
