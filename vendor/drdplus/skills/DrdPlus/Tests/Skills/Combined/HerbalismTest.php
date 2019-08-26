<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Tests\Skills\WithBonusToSensesTrait;

class HerbalismTest extends WithBonusToIntelligenceFromCombinedTest
{
    use WithBonusToSensesTrait;

    /**
     * @param int $currentSkillRankValue
     * @return int
     */
    protected function getExpectedBonusToSenses(int $currentSkillRankValue): int
    {
        return 3 + 3 * $currentSkillRankValue;
    }

}