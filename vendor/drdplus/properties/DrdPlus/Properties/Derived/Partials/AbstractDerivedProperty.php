<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Derived\Partials;

use DrdPlus\Properties\Derived\DerivedProperty;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;

/**
 * Unlike \DrdPlus\Properties\AbstractDerivedProperty this is not an enum because is not intended to be persisted (is
 * calculated / derived always)
 */
abstract class AbstractDerivedProperty extends StrictObject implements DerivedProperty
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @param int $value
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    protected function __construct($value)
    {
        $this->value = ToInteger::toInteger($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int|IntegerInterface $value
     * @return AbstractDerivedProperty
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function add($value): AbstractDerivedProperty
    {
        $increased = clone $this;
        $increased->value += ToInteger::toInteger($value);

        return $increased;
    }

    /**
     * @param int|IntegerInterface $value
     * @return AbstractDerivedProperty
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function sub($value): AbstractDerivedProperty
    {
        $decreased = clone $this;
        $decreased->value -= ToInteger::toInteger($value);

        return $decreased;
    }
}