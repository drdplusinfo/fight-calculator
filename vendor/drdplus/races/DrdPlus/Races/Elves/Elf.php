<?php
declare(strict_types=1);

namespace DrdPlus\Races\Elves;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Races\Race;

abstract class Elf extends Race
{
    public const ELF = RaceCode::ELF;

    /**
     * @param SubRaceCode $subRaceCode
     * @return Elf|Race
     */
    protected static function getItBySubRace(SubRaceCode $subRaceCode): Elf
    {
        return parent::getItByRaceAndSubRace(RaceCode::getIt(RaceCode::ELF), $subRaceCode);
    }

    public function getRaceCode(): RaceCode
    {
        return RaceCode::getIt(RaceCode::ELF);
    }

}