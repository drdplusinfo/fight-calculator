<?php declare(strict_types = 1);

declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Granam\Integer;

use Granam\Integer\Tools\ToInteger;
use Granam\Number\NegativeNumberObject;

/**
 * @method IntegerWithHistory add(int|float|\Granam\Number\NumberInterface $value)
 * @method IntegerWithHistory sub(int|float|\Granam\Number\NumberInterface $value)
 */
class NegativeIntegerObject extends NegativeNumberObject implements NegativeInteger
{

    /**
     * @param mixed $value
     * @param bool $strict = true allows only explicit values, not null and empty string
     * @param bool $paranoid Throws exception if some value is lost on cast because of rounding
     * @throws \Granam\Integer\Tools\Exceptions\NegativeIntegerCanNotBePositive
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function __construct($value, bool $strict = true, bool $paranoid = false)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        parent::__construct(ToInteger::toNegativeInteger($value, $strict, $paranoid));
    }

}