<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Psychical;

class SocialEtiquetteTest extends WithBonusToCharismaFromPsychicalTest
{
    /**
     * @param int $skillRankValue
     * @return int
     * @throws \LogicException
     */
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return $skillRankValue;
    }
}