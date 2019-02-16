<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical\RollsOnQuality\RollsOnSuccess;

use DrdPlus\Skills\Physical\RollsOnQuality\BlacksmithingQuality;
use DrdPlus\Skills\Physical\RollsOnQuality\RollsOnSuccess\BlacksmithingRollOnSuccess;
use Granam\Tests\Tools\TestWithMockery;

class BlacksmithingRollOnSuccessTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideDifficulty
     * @param int $difficulty
     */
    public function I_can_get_both_success_and_failure(int $difficulty)
    {
        $success = new BlacksmithingRollOnSuccess(
            $difficulty,
            $blacksmithingQuality = $this->createBlacksmithingQuality($difficulty /* quality is same as difficulty */)
        );
        self::assertSame($difficulty, $success->getDifficulty());
        self::assertSame($blacksmithingQuality, $success->getRollOnQuality());
        self::assertTrue($success->isSuccess());
        self::assertFalse($success->isFailure());
        self::assertSame(BlacksmithingRollOnSuccess::DEFAULT_SUCCESS_RESULT_CODE, $success->getResult());

        $failure = new BlacksmithingRollOnSuccess(
            $difficulty,
            $blacksmithingQuality = $this->createBlacksmithingQuality($difficulty - 1)
        );
        self::assertSame($difficulty, $failure->getDifficulty());
        self::assertSame($blacksmithingQuality, $failure->getRollOnQuality());
        self::assertFalse($failure->isSuccess());
        self::assertTrue($failure->isFailure());
        self::assertSame(BlacksmithingRollOnSuccess::DEFAULT_FAILURE_RESULT_CODE, $failure->getResult());
    }

    public function provideDifficulty(): array
    {
        return array_map(
            function (int $value) {
                return [$value];
            },
            range(-5, 5, 1)
        );
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|BlacksmithingQuality
     */
    private function createBlacksmithingQuality(int $value)
    {
        $blacksmithingQuality = $this->mockery(BlacksmithingQuality::class);
        $blacksmithingQuality->shouldReceive('getValue')
            ->andReturn($value);

        return $blacksmithingQuality;
    }
}