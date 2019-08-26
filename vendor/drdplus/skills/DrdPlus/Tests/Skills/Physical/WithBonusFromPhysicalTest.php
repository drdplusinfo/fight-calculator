<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Tests\Skills\WithBonusTest;

abstract class WithBonusFromPhysicalTest extends WithBonusTest
{
    /**
     * @return \Mockery\MockInterface|PhysicalSkillPoint|SkillPoint
     */
    protected function createSkillPoint(): SkillPoint
    {
        $physicalSkillPoint = $this->mockery(PhysicalSkillPoint::class);
        $physicalSkillPoint->shouldReceive('getValue')
            ->andReturn(1);

        return $physicalSkillPoint;
    }

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