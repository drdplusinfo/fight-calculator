<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\CombatCharacteristicCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Properties\Combat\Partials\CombatCharacteristic;

/**
 * See PPH page 34 left column, @link https://pph.drdplus.info/#tabulka_bojovych_charakteristik
 * Defense can change only with Agility.
 */
class Defense extends CombatCharacteristic
{
    /**
     * @param Agility $agility
     * @return Defense
     */
    public static function getIt(Agility $agility): Defense
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static(SumAndRound::ceiledHalf($agility->getValue()));
    }

    /**
     * @return CombatCharacteristicCode
     */
    public function getCode(): CombatCharacteristicCode
    {
        return CombatCharacteristicCode::getIt(CombatCharacteristicCode::DEFENSE);
    }
}