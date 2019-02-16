<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\Combined\Cooking;
use DrdPlus\Tests\Skills\SkillRankTest;

class CombinedSkillRankTest extends SkillRankTest
{

    /**
     * @param $value
     * @return \Mockery\MockInterface|CombinedSkillPoint
     */
    protected function createSkillPoint($value = null)
    {
        $combinedSkillPoint = $this->mockery(CombinedSkillPoint::class);
        $this->addProfessionLevelGetter($combinedSkillPoint);
        if ($value !== null) {
            $combinedSkillPoint->shouldReceive('getValue')
                ->andReturn($value);
        }

        return $combinedSkillPoint;
    }

    protected function createOwningSkill(): \DrdPlus\Skills\Skill
    {
        return new Cooking($this->createProfessionFirstLevel());
    }

}