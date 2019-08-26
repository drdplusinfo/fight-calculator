<?php declare(strict_types=1);

namespace DrdPlus\Races\Orcs;

use DrdPlus\Codes\SubRaceCode;

class CommonOrc extends Orc
{
    public const COMMON = SubRaceCode::COMMON;

    /**
     * @return Orc|CommonOrc
     */
    public static function getIt(): CommonOrc
    {
        return parent::getItBySubrace(SubRaceCode::getIt(SubRaceCode::COMMON));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::COMMON);
    }

}