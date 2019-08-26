<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\SkillPoint;

/**
 * @method \Mockery\MockInterface mockery(string $class)
 */
trait CreatePhysicalSkillPointTrait
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
}