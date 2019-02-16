<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Tests\Skills\WithBonusToKnackTest;

abstract class WithBonusToKnackFromCombinedTest extends WithBonusToKnackTest
{
    /**
     * @return \Mockery\MockInterface|CombinedSkillPoint|SkillPoint
     */
    protected function createSkillPoint(): SkillPoint
    {
        $combinedSkillPoint = $this->mockery(CombinedSkillPoint::class);
        $combinedSkillPoint->shouldReceive('getValue')
            ->andReturn(1);

        return $combinedSkillPoint;
    }
}