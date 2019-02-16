<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Combat\Partials;

use DrdPlus\Tables\Measurements\Distance\DistanceBonus;
use DrdPlus\Tables\Tables;

abstract class AbstractRange extends CharacteristicForGame
{
    /**
     * @param Tables $tables
     * @return float
     */
    public function getInMeters(Tables $tables): float
    {
        // both encounter and maximal ranges are in fact distance bonuses
        return (new DistanceBonus($this->getValue(), $tables->getDistanceTable()))->getDistance()->getMeters();
    }
}