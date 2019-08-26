<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Tables\Tables;

/**
 * @method Senses add(int | \Granam\Integer\IntegerInterface $value)
 * @method Senses sub(int | \Granam\Integer\IntegerInterface $value)
 */
class Senses extends AbstractDerivedProperty
{
    public static function getIt(Knack $knack, RaceCode $raceCode, SubRaceCode $subRaceCode, Tables $tables): Senses
    {
        return new static($knack->getValue() + $tables->getRacesTable()->getSenses($raceCode, $subRaceCode));
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::SENSES);
    }
}