<?php declare(strict_types=1);

namespace DrdPlus\Races\Krolls;

use DrdPlus\Codes\SubRaceCode;

class CommonKroll extends Kroll
{
    public const COMMON = SubRaceCode::COMMON;

    /**
     * @return Kroll|CommonKroll
     */
    public static function getIt(): CommonKroll
    {
        return parent::getItBySubrace(SubRaceCode::getIt(SubRaceCode::COMMON));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::COMMON);
    }

}