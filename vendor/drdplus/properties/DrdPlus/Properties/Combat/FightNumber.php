<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\Armaments\MeleeWeaponlikeCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\Properties\CharacteristicForGameCode;
use DrdPlus\Properties\Combat\Partials\CharacteristicForGame;
use DrdPlus\Tables\Tables;

/**
 * @method FightNumber add(int | \Granam\Integer\IntegerInterface $value)
 * @method FightNumber sub(int | \Granam\Integer\IntegerInterface $value)
 */
class FightNumber extends CharacteristicForGame
{
    /**
     * @param Fight $fight
     * @param WeaponlikeCode $weaponlikeCode
     * @param Tables $tables
     * @return FightNumber
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     */
    public static function getIt(Fight $fight, WeaponlikeCode $weaponlikeCode, Tables $tables): FightNumber
    {
        return new static($fight->getValue() + static::getLengthOfWeaponOrShield($weaponlikeCode, $tables));
    }

    /**
     * Length of a weapon (or shield) increases fight number.
     * Note about shield: every shield is considered as a weapon of length 0.
     *
     * @param WeaponlikeCode $weaponlikeCode
     * @param Tables $tables
     * @return int
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     */
    protected static function getLengthOfWeaponOrShield(WeaponlikeCode $weaponlikeCode, Tables $tables): int
    {
        if ($weaponlikeCode instanceof MeleeWeaponlikeCode) {
            return $tables->getMeleeWeaponlikeTableByMeleeWeaponlikeCode($weaponlikeCode)
                ->getLengthOf($weaponlikeCode);
        }

        return 0; // ranged weapons do not have bonus to fight number for their length, surprisingly
    }

    /**
     * @return CharacteristicForGameCode
     */
    public function getCode(): CharacteristicForGameCode
    {
        return CharacteristicForGameCode::getIt(CharacteristicForGameCode::FIGHT_NUMBER);
    }
}