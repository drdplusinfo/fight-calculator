<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess;

class HandworkSimpleRollOnModerateSuccessTest extends HandworkSimpleRollOnSuccessTest
{
    /**
     * @return int
     */
    protected function getExpectedDifficulty(): int
    {
        return 17;
    }

    /**
     * @return string
     */
    protected function getExpectedSuccessValue(): string
    {
        return 'handy';
    }

    /**
     * @return string
     */
    protected function getExpectedFailureValue(): string
    {
        return 'usable';
    }
}