<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\CombatCharacteristicCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Properties\Combat\Partials\CombatCharacteristic;

/**
 * See PPH page 34 left column , @link https://pph.drdplus.info/#tabulka_bojovych_charakteristik
 * Attack can change only with Agility.
 */
class Attack extends CombatCharacteristic
{

    /**
     * @param Agility $agility
     * @return Attack
     */
    public static function getIt(Agility $agility): Attack
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static(SumAndRound::flooredHalf($agility->getValue()));
    }

    /**
     * @return CombatCharacteristicCode
     */
    public function getCode(): CombatCharacteristicCode
    {
        return CombatCharacteristicCode::getIt(CombatCharacteristicCode::ATTACK);
    }

}