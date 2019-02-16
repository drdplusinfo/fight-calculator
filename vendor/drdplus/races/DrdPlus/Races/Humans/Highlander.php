<?php
declare(strict_types=1);

namespace DrdPlus\Races\Humans;

use DrdPlus\Codes\SubRaceCode;

class Highlander extends Human
{
    public const HIGHLANDER = SubRaceCode::HIGHLANDER;

    /**
     * @return Human|Highlander
     */
    public static function getIt(): Highlander
    {
        return parent::getItBySubRace(SubRaceCode::getIt(SubRaceCode::HIGHLANDER));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::HIGHLANDER);
    }

}