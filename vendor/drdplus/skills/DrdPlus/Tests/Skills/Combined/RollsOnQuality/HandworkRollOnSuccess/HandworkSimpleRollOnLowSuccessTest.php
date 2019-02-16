<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess;

class HandworkSimpleRollOnLowSuccessTest extends HandworkSimpleRollOnSuccessTest
{
    /**
     * @return int
     */
    protected function getExpectedDifficulty(): int
    {
        return 14;
    }

    /**
     * @return string
     */
    protected function getExpectedSuccessValue(): string
    {
        return 'usable';
    }

    /**
     * @return string
     */
    protected function getExpectedFailureValue(): string
    {
        return 'useless';
    }
}