<?php
declare(strict_types=1);

namespace DrdPlus\Races\Dwarfs;

use DrdPlus\Codes\SubRaceCode;

class MountainDwarf extends Dwarf
{

    public const MOUNTAIN = SubRaceCode::MOUNTAIN;

    /**
     * @return MountainDwarf|Dwarf
     */
    public static function getIt(): MountainDwarf
    {
        return parent::getItBySubRace(SubRaceCode::getIt(SubRaceCode::MOUNTAIN));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::MOUNTAIN);
    }

}