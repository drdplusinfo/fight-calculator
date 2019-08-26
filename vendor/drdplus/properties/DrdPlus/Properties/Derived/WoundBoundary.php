<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Tables\Measurements\Wounds\WoundsBonus;
use DrdPlus\Tables\Tables;

/**
 * @method WoundBoundary add(int | \Granam\Integer\IntegerInterface $value)
 * @method WoundBoundary sub(int | \Granam\Integer\IntegerInterface $value)
 */
class WoundBoundary extends AbstractDerivedProperty
{
    public static function getIt(Toughness $toughness, Tables $tables): WoundBoundary
    {
        return new static(
            $tables->getWoundsTable()->toWounds(
                new WoundsBonus($toughness->getValue() + 10, $tables->getWoundsTable())
            )->getValue()
        );
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::WOUND_BOUNDARY);
    }
}