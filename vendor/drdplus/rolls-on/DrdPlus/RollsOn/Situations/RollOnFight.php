<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\Situations;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;

class RollOnFight extends RollOnSituation
{
    /**
     * @var int
     */
    private $fightNumber;

    /**
     * RollOnFight constructor.
     *
     * @param int|IntegerInterface $fightNumber
     * @param Roll2d6DrdPlus $roll2d6Plus
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function __construct($fightNumber, Roll2d6DrdPlus $roll2d6Plus)
    {
        parent::__construct($roll2d6Plus);
        $this->fightNumber = ToInteger::toInteger($fightNumber);
    }

    public function getFightNumber(): int
    {
        return $this->fightNumber;
    }

    public function getValue(): int
    {
        return $this->getFightNumber() + $this->getRoll2d6Plus()->getValue();
    }
}