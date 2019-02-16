<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Tables\Measurements\Fatigue\FatigueBonus;
use DrdPlus\Tables\Tables;

/**
 * @method FatigueBoundary add(int | \Granam\Integer\IntegerInterface $value)
 * @method FatigueBoundary sub(int | \Granam\Integer\IntegerInterface $value)
 */
class FatigueBoundary extends AbstractDerivedProperty
{
    public static function getIt(Endurance $endurance, Tables $tables): FatigueBoundary
    {
        return new static(
            $tables->getFatigueTable()->toFatigue(
                new FatigueBonus(
                    $endurance->getValue() + 10,
                    $tables->getFatigueTable()
                )
            )->getValue()
        );
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::FATIGUE_BOUNDARY);
    }
}