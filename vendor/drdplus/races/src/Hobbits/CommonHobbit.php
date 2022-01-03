<?php declare(strict_types=1);

namespace DrdPlus\Races\Hobbits;

use DrdPlus\Codes\SubRaceCode;

class CommonHobbit extends Hobbit
{
    public const COMMON = SubRaceCode::COMMON;

    /**
     * @return Hobbit|CommonHobbit
     */
    public static function getIt(): CommonHobbit
    {
        return parent::getItBySubRace(SubRaceCode::getIt(SubRaceCode::COMMON));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::COMMON);
    }
}