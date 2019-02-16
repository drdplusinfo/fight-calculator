<?php
declare(strict_types=1);

namespace DrdPlus\Races\Humans;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Races\Race;

abstract class Human extends Race
{
    public const HUMAN = RaceCode::HUMAN;

    /**
     * @param SubRaceCode $subRaceCode
     * @return Race|Human
     */
    protected static function getItBySubRace(SubRaceCode $subRaceCode): Human
    {
        return parent::getItByRaceAndSubRace(RaceCode::getIt(RaceCode::HUMAN), $subRaceCode);
    }

    public function getRaceCode(): RaceCode
    {
        return RaceCode::getIt(RaceCode::HUMAN);
    }

}