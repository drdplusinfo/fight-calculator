<?php declare(strict_types=1);

namespace DrdPlus\Tables\Measurements\Partials;

use Granam\Integer\PositiveInteger;

abstract class AbstractPositiveIntegerMeasurementWithBonus extends AbstractMeasurementWithBonus implements PositiveInteger
{
    public function getValue(): int
    {
        return parent::getValue();
    }

}
