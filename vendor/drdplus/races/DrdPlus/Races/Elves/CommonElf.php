<?php
declare(strict_types = 1);

namespace DrdPlus\Races\Elves;

use DrdPlus\Codes\SubRaceCode;

class CommonElf extends Elf
{
    public const COMMON = SubRaceCode::COMMON;

    /**
     * @return Elf|CommonElf
     */
    public static function getIt(): CommonElf
    {
        return parent::getItBySubRace(SubRaceCode::getIt(SubRaceCode::COMMON));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::COMMON);
    }

}