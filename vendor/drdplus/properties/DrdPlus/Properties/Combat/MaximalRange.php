<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\Properties\CharacteristicForGameCode;
use DrdPlus\Properties\Combat\Partials\AbstractRange;

/**
 * @method MaximalRange add(int | \Granam\Integer\IntegerInterface $value)
 * @method MaximalRange sub(int | \Granam\Integer\IntegerInterface $value)
 */
class MaximalRange extends AbstractRange
{
    /**
     * Well, sadly maximal range of a weapon is same as encounter range and that is zero (melee respectively).
     *
     * @param EncounterRange $encounterRange
     * @return MaximalRange
     */
    public static function getItForMeleeWeapon(EncounterRange $encounterRange): MaximalRange
    {
        return new static($encounterRange->getValue());
    }

    /**
     * See PPH page 95 left column.
     *
     * @param EncounterRange $encounterRange
     * @return MaximalRange
     */
    public static function getItForRangedWeapon(EncounterRange $encounterRange): MaximalRange
    {
        return new static($encounterRange->getValue() + 12);
    }

    /**
     * @return CharacteristicForGameCode
     */
    public function getCode(): CharacteristicForGameCode
    {
        return CharacteristicForGameCode::getIt(CharacteristicForGameCode::MAXIMAL_RANGE);
    }
}