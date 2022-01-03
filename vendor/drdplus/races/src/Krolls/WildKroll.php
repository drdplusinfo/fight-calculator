<?php declare(strict_types=1);

namespace DrdPlus\Races\Krolls;

use DrdPlus\Codes\SubRaceCode;

class WildKroll extends Kroll
{
    public const WILD = SubRaceCode::WILD;

    /**
     * @return Kroll|WildKroll
     */
    public static function getIt(): WildKroll
    {
        return parent::getItBySubrace(SubRaceCode::getIt(SubRaceCode::WILD));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::WILD);
    }
}