<?php declare(strict_types=1);

namespace DrdPlus\Tables\Measurements\Experiences;

use DrdPlus\Tables\Measurements\Partials\AbstractMeasurementWithBonus;
use DrdPlus\Tables\Measurements\Partials\AbstractPositiveIntegerMeasurementWithBonus;
use Granam\Integer\Tools\ToInteger;

/**
 * @see \DrdPlus\Tables\Measurements\Experiences\Experiences::normalizeValue
 */
class Experiences extends AbstractPositiveIntegerMeasurementWithBonus
{
    public const EXPERIENCES = 'experiences';

    private \DrdPlus\Tables\Measurements\Experiences\ExperiencesTable $experiencesTable;

    public function __construct($value, ExperiencesTable $experiencesTable, $unit = self::EXPERIENCES)
    {
        parent::__construct($value, $unit);
        $this->experiencesTable = $experiencesTable;
    }

    /**
     * @param mixed $value
     * @return int
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    protected function normalizeValue($value): int
    {
        return ToInteger::toInteger($value);
    }

    /**
     * @return array|string[]
     */
    public function getPossibleUnits(): array
    {
        return [self::EXPERIENCES];
    }

    /**
     * @return Level
     */
    public function getLevel(): Level
    {
        return $this->getBonus();
    }

    /**
     * Final level, achieved by sparing current experiences from total zero
     *
     * @return Level
     */
    public function getTotalLevel(): Level
    {
        return $this->experiencesTable->toTotalLevel($this);
    }

    /**
     * @return Level
     */
    public function getBonus(): Level
    {
        return $this->experiencesTable->toLevel($this);
    }

}
