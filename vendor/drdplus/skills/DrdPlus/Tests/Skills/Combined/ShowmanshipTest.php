<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\Skills\Combined\RollsOnQuality\ShowmanshipGameQuality;
use DrdPlus\Skills\Combined\Showmanship;

class ShowmanshipTest extends WithBonusFromCombinedTest
{
    /**
     * @param int $skillRankValue
     * @return int
     */
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return 3 * $skillRankValue;
    }

    /**
     * @test
     */
    public function I_can_get_quality_of_game_of_showmanship()
    {
        $showmanship = new Showmanship($this->createProfessionLevel());
        $showmanship->increaseSkillRank($this->createSkillPoint());
        $showmanshipGameQuality = $showmanship->getShowmanshipGameQuality(
            $charisma = $this->createCharisma(123),
            $roll2D6DrdPlus = $this->createRoll2d6DrdPlus(465)
        );
        self::assertEquals(
            new ShowmanshipGameQuality($charisma, $showmanship, $roll2D6DrdPlus),
            $showmanshipGameQuality
        );
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|Charisma
     */
    private function createCharisma(int $value)
    {
        $charisma = $this->mockery(Charisma::class);
        $charisma->shouldReceive('getValue')
            ->andReturn($value);

        return $charisma;
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|Roll2d6DrdPlus
     */
    private function createRoll2d6DrdPlus(int $value)
    {
        $roll2d6DrdPlus = $this->mockery(Roll2d6DrdPlus::class);
        $roll2d6DrdPlus->shouldReceive('getValue')
            ->andReturn($value);

        return $roll2d6DrdPlus;
    }
}