<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\Properties\CharacteristicForGameCode;
use DrdPlus\Properties\Combat\Partials\PositiveIntegerCharacteristicForGame;
use Granam\Integer\PositiveInteger;

/**
 * @method LoadingInRounds add(int | \Granam\Integer\IntegerInterface $value)
 * @method LoadingInRounds sub(int | \Granam\Integer\IntegerInterface $value)
 */
class LoadingInRounds extends PositiveIntegerCharacteristicForGame
{
    /**
     * @param int|PositiveInteger $value
     * @return LoadingInRounds
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \Granam\Integer\Tools\Exceptions\PositiveIntegerCanNotBeNegative
     */
    public static function getIt($value): LoadingInRounds
    {
        return new static($value);
    }

    /**
     * @return CharacteristicForGameCode
     */
    public function getCode(): CharacteristicForGameCode
    {
        return CharacteristicForGameCode::getIt(CharacteristicForGameCode::LOADING_IN_ROUNDS);
    }
}