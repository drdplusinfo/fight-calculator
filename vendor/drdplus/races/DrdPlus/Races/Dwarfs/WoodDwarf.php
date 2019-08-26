<?php declare(strict_types=1);

namespace DrdPlus\Races\Dwarfs;

use DrdPlus\Codes\SubRaceCode;

class WoodDwarf extends Dwarf
{
    public const WOOD = SubRaceCode::WOOD;

    /**
     * @return WoodDwarf|Dwarf
     */
    public static function getIt(): WoodDwarf
    {
        return parent::getItBySubRace(SubRaceCode::getIt(SubRaceCode::WOOD));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::WOOD);
    }

}