<?php declare(strict_types=1);

namespace DrdPlus\Tables\Measurements\Fatigue;

use DrdPlus\Tables\Measurements\Partials\AbstractPositiveIntegerMeasurementWithBonus;
use Granam\Integer\Tools\Exceptions\PositiveIntegerCanNotBeNegative;
use Granam\Integer\Tools\ToInteger;

/**
 * @see Fatigue::normalizeValue()
 */
class Fatigue extends AbstractPositiveIntegerMeasurementWithBonus
{
    public const FATIGUE = 'fatigue';

    private \DrdPlus\Tables\Measurements\Fatigue\FatigueTable $fatigueTable;

    /**
     * @param float|int|\Granam\Number\NumberInterface $value
     * @param FatigueTable $fatigueTable
     * @throws \DrdPlus\Tables\Measurements\Fatigue\Exceptions\FatigueCanNotBeNegative
     * @throws \Granam\Scalar\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Float\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Float\Tools\Exceptions\ValueLostOnCast
     */
    public function __construct($value, FatigueTable $fatigueTable)
    {
        try {
            parent::__construct($value, self::FATIGUE);
        } catch (PositiveIntegerCanNotBeNegative $exception) {
            throw new Exceptions\FatigueCanNotBeNegative(
                'Fatigue has to be positive integer with least zero as \'rested\', given ' . \var_export($value, true)
            );
        }
        $this->fatigueTable = $fatigueTable;
    }

    /**
     * @param mixed $value
     * @return int
     * @throws \Granam\Integer\Tools\Exceptions\PositiveIntegerCanNotBeNegative
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    protected function normalizeValue($value): int
    {
        return ToInteger::toPositiveInteger($value);
    }

    /**
     * @return array|string[]
     */
    public function getPossibleUnits(): array
    {
        return [self::FATIGUE];
    }

    /**
     * @return FatigueBonus
     */
    public function getBonus(): FatigueBonus
    {
        return $this->fatigueTable->toBonus($this);
    }

}
