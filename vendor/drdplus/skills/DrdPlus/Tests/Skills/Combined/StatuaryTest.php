<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace DrdPlus\Tests\Skills\Combined;

class StatuaryTest extends WithBonusFromCombinedTest
{
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return 3 * $skillRankValue;
    }
}