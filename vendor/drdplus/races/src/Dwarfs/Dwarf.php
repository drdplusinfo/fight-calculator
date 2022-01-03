<?php declare(strict_types=1);

namespace DrdPlus\Races\Dwarfs;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Races\Race;

abstract class Dwarf extends Race
{
    public const DWARF = RaceCode::DWARF;

    /**
     * @param SubRaceCode $subRaceCode
     * @return Dwarf|Race
     */
    protected static function getItBySubRace(SubRaceCode $subRaceCode): Dwarf
    {
        return parent::getItByRaceAndSubRace(RaceCode::getIt(RaceCode::DWARF), $subRaceCode);
    }

    public function getRaceCode(): RaceCode
    {
        return RaceCode::getIt(RaceCode::DWARF);
    }

}