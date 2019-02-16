<?php
declare(strict_types = 1);

namespace DrdPlus\Races\Krolls;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Races\Race;

abstract class Kroll extends Race
{
    public const KROLL = RaceCode::KROLL;

    /**
     * @param SubRaceCode $subRaceCode
     * @return Race|Kroll
     */
    protected static function getItBySubrace(SubRaceCode $subRaceCode)
    {
        return parent::getItByRaceAndSubRace(RaceCode::getIt(RaceCode::KROLL), $subRaceCode);
    }

    public function getRaceCode(): RaceCode
    {
        return RaceCode::getIt(RaceCode::KROLL);
    }

}