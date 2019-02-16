<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\SkillPoint;

/**
 * @method \Mockery\MockInterface mockery(string $class)
 */
trait CreateCombinedSkillPointTrait
{
    /**
     * @return \Mockery\MockInterface|CombinedSkillPoint|SkillPoint
     */
    protected function createSkillPoint(): SkillPoint
    {
        $physicalSkillPoint = $this->mockery(CombinedSkillPoint::class);
        $physicalSkillPoint->shouldReceive('getValue')
            ->andReturn(1);

        return $physicalSkillPoint;
    }
}