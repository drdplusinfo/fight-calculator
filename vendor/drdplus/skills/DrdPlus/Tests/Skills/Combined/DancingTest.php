<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use Granam\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\Skills\Combined\Dancing;
use DrdPlus\Skills\Combined\RollsOnQuality\DanceQuality;

class DancingTest extends WithBonusFromCombinedTest
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
    public function I_can_create_dance_quality_by_it_easier()
    {
        $dancing = new Dancing($this->createProfessionLevel());
        $agility = Agility::getIt(9);
        $roll2D6DrdPlus = Roller2d6DrdPlus::getIt()->roll();
        $danceQuality = $dancing->createDanceQuality($agility, $roll2D6DrdPlus);
        self::assertEquals(new DanceQuality($agility, $dancing, $roll2D6DrdPlus), $danceQuality);

        $dancing->increaseSkillRank($this->createSkillPoint());
        $higherDanceQuality = $dancing->createDanceQuality($agility, $roll2D6DrdPlus);
        self::assertNotEquals($danceQuality, $higherDanceQuality);
        self::assertEquals(new DanceQuality($agility, $dancing, $roll2D6DrdPlus), $higherDanceQuality);
    }

}