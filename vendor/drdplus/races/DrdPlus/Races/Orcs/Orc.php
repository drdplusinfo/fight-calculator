<?php
declare(strict_types=1);

namespace DrdPlus\Races\Orcs;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Races\Race;

abstract class Orc extends Race
{
    public const ORC = RaceCode::ORC;

    /**
     * @param SubRaceCode $subRaceCode
     * @return Race|Orc
     */
    protected static function getItBySubrace(SubRaceCode $subRaceCode): Orc
    {
        return parent::getItByRaceAndSubRace(RaceCode::getIt(RaceCode::ORC), $subRaceCode);
    }

    public function getRaceCode(): RaceCode
    {
        return RaceCode::getIt(RaceCode::ORC);
    }

}