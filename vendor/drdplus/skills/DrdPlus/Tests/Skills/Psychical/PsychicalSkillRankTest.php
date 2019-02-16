<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills\Psychical;

use DrdPlus\Skills\Psychical\Botany;
use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Tests\Skills\SkillRankTest;

class PsychicalSkillRankTest extends SkillRankTest
{

    /**
     * @param $value
     * @return \Mockery\MockInterface|PsychicalSkillPoint
     */
    protected function createSkillPoint($value = null)
    {
        $psychicalSkillPoint = $this->mockery(PsychicalSkillPoint::class);
        $this->addProfessionLevelGetter($psychicalSkillPoint);
        if ($value !== null) {
            $psychicalSkillPoint->shouldReceive('getValue')
                ->andReturn($value);
        }

        return $psychicalSkillPoint;
    }

    protected function createOwningSkill(): \DrdPlus\Skills\Skill
    {
        return new Botany($this->createProfessionFirstLevel());
    }

}