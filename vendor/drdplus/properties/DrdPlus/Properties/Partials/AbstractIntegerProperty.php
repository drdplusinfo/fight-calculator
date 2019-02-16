<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Partials;

use DrdPlus\BaseProperties\Property;
use DrdPlus\Codes\Properties\PropertyCode;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;
use Granam\IntegerEnum\IntegerEnum;

/**
 * @method PropertyCode getCode()
 */
abstract class AbstractIntegerProperty extends IntegerEnum implements Property
{
    /**
     * @param int|IntegerInterface $value
     * @return AbstractIntegerProperty
     */
    public static function getIt($value)
    {
        return static::getEnum($value);
    }

    /**
     * @param int|IntegerInterface $value
     * @return AbstractIntegerProperty|IntegerEnum
     */
    public static function getEnum($value): IntegerEnum
    {
        return new static($value);
    }

    /**
     * @param int|IntegerInterface $value
     * @return AbstractIntegerProperty
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function add($value)
    {
        return static::getIt($this->getValue() + ToInteger::toInteger($value));
    }

    /**
     * @param int|IntegerInterface $value
     * @return AbstractIntegerProperty
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function sub($value)
    {
        return static::getIt($this->getValue() - ToInteger::toInteger($value));
    }
}