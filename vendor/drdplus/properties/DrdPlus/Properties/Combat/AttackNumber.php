<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\Properties\CharacteristicForGameCode;
use DrdPlus\Properties\Combat\Partials\CharacteristicForGame;

/**
 * See PPH page 92 right column, @link https://pph.drdplus.info/#utocne_cislo_uc
 * Attack number can be affected by many ways unlike Attack.
 *
 * @method AttackNumber add(int | \Granam\Integer\IntegerInterface $value)
 * @method AttackNumber sub(int | \Granam\Integer\IntegerInterface $value)
 */
class AttackNumber extends CharacteristicForGame
{
    /**
     * @param Attack $attack
     * @return AttackNumber
     */
    public static function getItFromAttack(Attack $attack): AttackNumber
    {
        return new static($attack->getValue());
    }

    /**
     * @param Shooting $shooting
     * @return AttackNumber
     */
    public static function getItFromShooting(Shooting $shooting): AttackNumber
    {
        return new static($shooting->getValue());
    }

    /**
     * @return CharacteristicForGameCode
     */
    public function getCode(): CharacteristicForGameCode
    {
        return CharacteristicForGameCode::getIt(CharacteristicForGameCode::ATTACK_NUMBER);
    }
}