<?php declare(strict_types=1);

namespace DrdPlus\Races\Orcs;

use DrdPlus\Codes\SubRaceCode;

class Goblin extends Orc
{
    public const GOBLIN = SubRaceCode::GOBLIN;

    /**
     * @return Orc|Goblin
     */
    public static function getIt(): Goblin
    {
        return parent::getItBySubrace(SubRaceCode::getIt(SubRaceCode::GOBLIN));
    }

    public function getSubRaceCode(): SubRaceCode
    {
        return SubRaceCode::getIt(SubRaceCode::GOBLIN);
    }

}