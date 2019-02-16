<?php
declare(strict_types=1);

namespace DrdPlus\Races\Elves;

use DrdPlus\Codes\SubRaceCode;

class GreenElf extends Elf
{
    public const GREEN = SubRaceCode::GREEN;

    /**
     * @return GreenElf|Elf
     */
    public static function getIt(): GreenElf
    {
        return parent::getItBySubRace(SubRaceCode::getIt(SubRaceCode::GREEN));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::GREEN);
    }

}