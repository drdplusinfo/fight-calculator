<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use Granam\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Combined\RollsOnQuality\SingingQuality;
use DrdPlus\Skills\Combined\Singing;

class SingingTest extends WithBonusFromCombinedTest
{
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return 3 * $skillRankValue;
    }

    /**
     * @test
     */
    public function I_can_find_out_if_can_imitate_bird()
    {
        $singing = new Singing($this->createProfessionLevel());
        self::assertFalse($singing->canImitateBirdSong());
        $singing->increaseSkillRank($this->createSkillPoint());
        self::assertFalse($singing->canImitateBirdSong());
        $singing->increaseSkillRank($this->createSkillPoint());
        self::assertFalse($singing->canImitateBirdSong());
        $singing->increaseSkillRank($this->createSkillPoint());
        self::assertTrue($singing->canImitateBirdSong());
    }

    /**
     * @test
     */
    public function I_can_get_signing_quality()
    {
        $singing = new Singing($this->createProfessionLevel());
        $knack = Knack::getIt(123);
        $roll = Roller2d6DrdPlus::getIt()->roll();
        $singingQuality = $singing->createSingingQuality($knack, $roll);
        self::assertEquals(new SingingQuality($knack, $singing, $roll), $singingQuality);
    }
}