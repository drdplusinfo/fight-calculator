<?php
declare(strict_types=1);

namespace DrdPlus\Races\Dwarfs;

use DrdPlus\Codes\SubRaceCode;

class CommonDwarf extends Dwarf
{
    public const COMMON = SubRaceCode::COMMON;

    /**
     * @return CommonDwarf|Dwarf
     */
    public static function getIt(): CommonDwarf
    {
        return parent::getItBySubRace(SubRaceCode::getIt(SubRaceCode::COMMON));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::COMMON);
    }

}