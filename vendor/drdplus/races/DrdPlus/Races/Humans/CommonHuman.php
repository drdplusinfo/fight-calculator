<?php
declare(strict_types=1);

namespace DrdPlus\Races\Humans;

use DrdPlus\Codes\SubRaceCode;

class CommonHuman extends Human
{
    public const COMMON = SubRaceCode::COMMON;

    /**
     * @return Human|CommonHuman
     */
    public static function getIt(): CommonHuman
    {
        return parent::getItBySubRace(SubRaceCode::getIt(SubRaceCode::COMMON));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::COMMON);
    }

}