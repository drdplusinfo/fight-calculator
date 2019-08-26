<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

class KnottingTest extends WithBonusToKnackFromCombinedTest
{
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return $skillRankValue;
    }

}