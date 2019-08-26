<?php declare(strict_types=1);

namespace DrdPlus\Races\Hobbits;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Races\Race;

abstract class Hobbit extends Race
{
    public const HOBBIT = RaceCode::HOBBIT;

    /**
     * @param SubRaceCode $subRaceCode
     * @return Race|Hobbit
     */
    protected static function getItBySubRace(SubRaceCode $subRaceCode): Hobbit
    {
        return parent::getItByRaceAndSubRace(RaceCode::getIt(RaceCode::HOBBIT), $subRaceCode);
    }

    public function getRaceCode(): RaceCode
    {
        return RaceCode::getIt(RaceCode::HOBBIT);
    }
}