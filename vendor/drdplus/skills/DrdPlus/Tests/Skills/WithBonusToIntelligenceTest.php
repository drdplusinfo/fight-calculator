<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\Skills\WithBonusToIntelligence;

abstract class WithBonusToIntelligenceTest extends WithBonusTest
{
    protected function getExpectedInterface(): string
    {
        return WithBonusToIntelligence::class;
    }

    /**
     * @param int $skillRankValue
     * @return int
     * @throws \LogicException
     */
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return 3 * $skillRankValue;
    }
}