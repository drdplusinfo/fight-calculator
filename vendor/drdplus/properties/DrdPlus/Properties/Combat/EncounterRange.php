<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\Properties\CharacteristicForGameCode;
use DrdPlus\Properties\Combat\Partials\AbstractRange;
use Granam\Integer\IntegerInterface;

/**
 * @method EncounterRange add(int | \Granam\Integer\IntegerInterface $value)
 * @method EncounterRange sub(int | \Granam\Integer\IntegerInterface $value)
 */
class EncounterRange extends AbstractRange
{
    /**
     * @param int|IntegerInterface $value
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \Granam\Integer\Tools\Exceptions\PositiveIntegerCanNotBeNegative
     * @return EncounterRange
     */
    public static function getIt($value): EncounterRange
    {
        return new static($value);
    }

    /**
     * @return CharacteristicForGameCode
     */
    public function getCode(): CharacteristicForGameCode
    {
        return CharacteristicForGameCode::getIt(CharacteristicForGameCode::ENCOUNTER_RANGE);
    }
}