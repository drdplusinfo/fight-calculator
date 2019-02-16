<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Combined\RollsOnQuality;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Skills\Combined\PlayingOnMusicInstrument;

/**
 * See PPH page 153 right column top, @link https://pph.drdplus.info/#vypocet_kvality_hry_na_hudebni_nastroj
 * @method Roll2d6DrdPlus getRoll()
 */
class PlayingOnMusicInstrumentGameQuality extends RollOnQuality
{
    /**
     * @param Knack $knack
     * @param PlayingOnMusicInstrument $playingOnMusicInstrument
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     */
    public function __construct(
        Knack $knack,
        PlayingOnMusicInstrument $playingOnMusicInstrument,
        Roll2d6DrdPlus $roll2D6DrdPlus
    )
    {
        parent::__construct($knack->getValue() + $playingOnMusicInstrument->getBonus(), $roll2D6DrdPlus);
    }

}