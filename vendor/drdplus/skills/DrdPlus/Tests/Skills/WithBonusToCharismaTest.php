<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\Skills\WithBonusToCharisma;

abstract class WithBonusToCharismaTest extends WithBonusTest
{
    protected function getExpectedInterface(): string
    {
        return WithBonusToCharisma::class;
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