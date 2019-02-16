<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\Properties\CharacteristicForGameCode;
use DrdPlus\Properties\Combat\Partials\CharacteristicForGame;

/**
 * See PPH page 92 right column, @link https://pph.drdplus.info/#obranne_cislo_oc
 * Defense number can be affected by many ways unlike Defense.
 *
 * @method DefenseNumber add(int | \Granam\Integer\IntegerInterface $value)
 * @method DefenseNumber sub(int | \Granam\Integer\IntegerInterface $value)
 */
class DefenseNumber extends CharacteristicForGame
{
    /**
     * @param Defense $defense
     * @return DefenseNumber
     */
    public static function getIt(Defense $defense): DefenseNumber
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static($defense->getValue());
    }

    /**
     * @return CharacteristicForGameCode
     */
    public function getCode(): CharacteristicForGameCode
    {
        return CharacteristicForGameCode::getIt(CharacteristicForGameCode::DEFENSE_NUMBER);
    }
}