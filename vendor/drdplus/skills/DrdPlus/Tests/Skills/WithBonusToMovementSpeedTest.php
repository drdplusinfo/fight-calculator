<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Skills\WithBonusToMovementSpeed;

abstract class WithBonusToMovementSpeedTest extends WithBonusTest
{
    protected function getExpectedInterface(): string
    {
        return WithBonusToMovementSpeed::class;
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
}