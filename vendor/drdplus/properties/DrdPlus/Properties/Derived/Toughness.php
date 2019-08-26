<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Tables\Tables;

/**
 * @method Toughness add(int | \Granam\Integer\IntegerInterface $value)
 * @method Toughness sub(int | \Granam\Integer\IntegerInterface $value)
 */
class Toughness extends AbstractDerivedProperty
{
    public static function getIt(Strength $strength, RaceCode $raceCode, SubRaceCode $subRaceCode, Tables $tables): Toughness
    {
        return new static($strength->getValue() + $tables->getRacesTable()->getToughness($raceCode, $subRaceCode));
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::TOUGHNESS);
    }
}