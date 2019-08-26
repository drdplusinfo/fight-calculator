<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Combined\PlayingOnMusicInstrument;
use DrdPlus\Skills\Combined\RollsOnQuality\PlayingOnMusicInstrumentGameQuality;

class PlayingOnMusicInstrumentTest extends WithBonusFromCombinedTest
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
    public function I_can_get_quality_of_game_when_playing_on_music_instrument()
    {
        $playingOnMusicInstrument = new PlayingOnMusicInstrument($this->createProfessionLevel());
        $playingOnMusicInstrument->increaseSkillRank($this->createSkillPoint());
        $playingOnMusicInstrumentGameQuality = $playingOnMusicInstrument->getPlayingOnMusicInstrumentGameQuality(
            $knack = $this->createKnack(123),
            $roll2D6DrdPlus = $this->createRoll2d6DrdPlus(465)
        );
        self::assertEquals(
            new PlayingOnMusicInstrumentGameQuality($knack, $playingOnMusicInstrument, $roll2D6DrdPlus),
            $playingOnMusicInstrumentGameQuality
        );
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|Knack
     */
    private function createKnack(int $value)
    {
        $knack = $this->mockery(Knack::class);
        $knack->shouldReceive('getValue')
            ->andReturn($value);

        return $knack;
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