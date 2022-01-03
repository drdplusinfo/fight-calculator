<?php declare(strict_types=1);

namespace DrdPlus\Health;

use Granam\Integer\PositiveIntegerObject;
use Granam\Integer\Tools\Exceptions\PositiveIntegerCanNotBeNegative;

class WoundSize extends PositiveIntegerObject
{
    /**
     * @param $value
     * @return WoundSize
     * @throws \DrdPlus\Health\Exceptions\WoundSizeCanNotBeNegative
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     */
    public static function createIt($value): WoundSize
    {
        return new static($value);
    }

    /**
     * @param mixed $value
     * @throws \DrdPlus\Health\Exceptions\WoundSizeCanNotBeNegative
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     */
    public function __construct($value)
    {
        try {
            parent::__construct($value);
        } catch (PositiveIntegerCanNotBeNegative $positiveIntegerCanNotBeNegative) {
            throw new Exceptions\WoundSizeCanNotBeNegative($positiveIntegerCanNotBeNegative->getMessage());
        }
    }
}