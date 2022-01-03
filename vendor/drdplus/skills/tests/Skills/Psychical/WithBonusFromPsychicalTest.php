<?php declare(strict_types=1);

namespace DrdPlus\Tests\Skills\Psychical;

use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Tests\Skills\WithBonusTest;

abstract class WithBonusFromPsychicalTest extends WithBonusTest
{
    /**
     * @return \Mockery\MockInterface|PsychicalSkillPoint|SkillPoint
     */
    protected function createSkillPoint(): SkillPoint
    {
        $psychicalSkillPoint = $this->mockery(PsychicalSkillPoint::class);
        $psychicalSkillPoint->shouldReceive('getValue')
            ->andReturn(1);

        return $psychicalSkillPoint;
    }
}
