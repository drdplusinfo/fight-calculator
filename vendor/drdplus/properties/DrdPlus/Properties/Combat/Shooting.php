<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\CombatCharacteristicCode;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Properties\Combat\Partials\CombatCharacteristic;

/**
 * See PPH page 34 left column, @link https://pph.drdplus.info/#tabulka_bojovych_charakteristik
 * Shooting can change only with Knack.
 */
class Shooting extends CombatCharacteristic
{
    /**
     * @param Knack $knack
     * @return Shooting
     */
    public static function getIt(Knack $knack): Shooting
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static(SumAndRound::flooredHalf($knack->getValue()));
    }

    /**
     * @return CombatCharacteristicCode
     */
    public function getCode(): CombatCharacteristicCode
    {
        return CombatCharacteristicCode::getIt(CombatCharacteristicCode::SHOOTING);
    }
}