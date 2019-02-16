<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Combat\Partials;

use DrdPlus\Properties\Combat\CombatProperty;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;

abstract class CombatCharacteristic extends StrictObject implements IntegerInterface, CombatProperty
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @param int
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    protected function __construct($value)
    {
        $this->value = $this->sanitizeValue($value);
    }

    /**
     * @param int|string|float|IntegerInterface $value
     * @return int
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    protected function sanitizeValue($value): int
    {
        return ToInteger::toInteger($value);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }
}