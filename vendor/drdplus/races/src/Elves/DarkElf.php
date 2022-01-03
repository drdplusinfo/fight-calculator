<?php declare(strict_types=1);

namespace DrdPlus\Races\Elves;

use DrdPlus\Codes\SubRaceCode;

class DarkElf extends Elf
{
    public const DARK = SubRaceCode::DARK;

    /**
     * @return DarkElf|Elf
     */
    public static function getIt(): DarkElf
    {
        return parent::getItBySubRace(SubRaceCode::getIt(SubRaceCode::DARK));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::DARK);
    }

}