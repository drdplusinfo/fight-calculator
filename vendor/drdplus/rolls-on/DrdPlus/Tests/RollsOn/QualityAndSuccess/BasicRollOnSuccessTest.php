<?php
declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\QualityAndSuccess;

use DrdPlus\RollsOn\QualityAndSuccess\BasicRollOnSuccess;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use Granam\Boolean\BooleanInterface;
use Granam\Tests\Tools\TestWithMockery;

class BasicRollOnSuccessTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideDifficultyAndPropertyWithRoll
     * @param $difficulty
     * @param RollOnQuality $rollOnQuality
     * @param $shouldSuccess
     */
    public function I_can_use_it($difficulty, RollOnQuality $rollOnQuality, $shouldSuccess)
    {
        $basicRollOnSuccess = new BasicRollOnSuccess($difficulty, $rollOnQuality);

        self::assertInstanceOf(BooleanInterface::class, $basicRollOnSuccess);
        self::assertSame($difficulty, $basicRollOnSuccess->getDifficulty());
        self::assertSame($rollOnQuality, $basicRollOnSuccess->getRollOnQuality());

        if ($shouldSuccess) {
            self::assertTrue($basicRollOnSuccess->getValue());
            self::assertTrue($basicRollOnSuccess->isSuccess());
            self::assertFalse($basicRollOnSuccess->isFailure());
            self::assertSame('success', (string)$basicRollOnSuccess);
        } else {
            self::assertFalse($basicRollOnSuccess->getValue());
            self::assertFalse($basicRollOnSuccess->isSuccess());
            self::assertTrue($basicRollOnSuccess->isFailure());
            self::assertSame('failure', (string)$basicRollOnSuccess);
        }
    }

    public function provideDifficultyAndPropertyWithRoll()
    {
        return [
            [123, $this->createRollOnQuality(789), true],
            [999, $this->createRollOnQuality(998), false],
            [0, $this->createRollOnQuality(0), true],
            [1, $this->createRollOnQuality(0), false],
            [1, $this->createRollOnQuality(1), true],
        ];
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|RollOnQuality
     */
    private function createRollOnQuality($value)
    {
        $rollOnQuality = $this->mockery(RollOnQuality::class);
        $rollOnQuality->shouldReceive('getValue')
            ->andReturn($value);

        return $rollOnQuality;
    }
}
