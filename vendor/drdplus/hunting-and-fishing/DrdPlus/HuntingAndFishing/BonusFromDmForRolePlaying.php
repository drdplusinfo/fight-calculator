<?php
declare(strict_types=1);

namespace DrdPlus\HuntingAndFishing;

use Granam\Integer\PositiveIntegerObject;

class BonusFromDmForRolePlaying extends PositiveIntegerObject
{
    public const MAXIMAL_BONUS_FROM_DM = 3;

    /**
     * @param mixed $value
     * @throws \DrdPlus\HuntingAndFishing\Exceptions\BonusFromDmIsTooHigh
     * @throws \Granam\Integer\Tools\Exceptions\PositiveIntegerCanNotBeNegative
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function __construct($value)
    {
        parent::__construct($value);
        if ($this->getValue() > self::MAXIMAL_BONUS_FROM_DM) {
            throw new Exceptions\BonusFromDmIsTooHigh(
                'Maximal allowed bonus is ' . self::MAXIMAL_BONUS_FROM_DM . ', got ' . $this->getValue()
            );
        }
    }
}