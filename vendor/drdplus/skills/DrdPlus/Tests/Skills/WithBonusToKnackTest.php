<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\Skills\WithBonusToKnack;

abstract class WithBonusToKnackTest extends WithBonusTest
{
    protected function getExpectedInterface(): string
    {
        return WithBonusToKnack::class;
    }

    /**
     * @param int $skillRankValue
     * @return int
     * @throws \LogicException
     */
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return 2 * $skillRankValue;
    }
}