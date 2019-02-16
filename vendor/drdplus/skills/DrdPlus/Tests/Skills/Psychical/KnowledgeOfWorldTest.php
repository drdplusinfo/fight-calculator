<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Psychical;

class KnowledgeOfWorldTest extends WithBonusFromPsychicalTest
{
    /**
     * @param int $skillRankValue
     * @return int
     */
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return 2 * $skillRankValue;
    }

}