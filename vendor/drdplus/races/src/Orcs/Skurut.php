<?php declare(strict_types=1);

namespace DrdPlus\Races\Orcs;

use DrdPlus\Codes\SubRaceCode;

class Skurut extends Orc
{
    public const SKURUT = SubRaceCode::SKURUT;

    /**
     * @return Orc|Skurut
     */
    public static function getIt(): Skurut
    {
        return parent::getItBySubrace(SubRaceCode::getIt(SubRaceCode::SKURUT));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::SKURUT);
    }

}