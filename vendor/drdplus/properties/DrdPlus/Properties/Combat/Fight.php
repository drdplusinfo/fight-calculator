<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\CharacteristicForGameCode;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Combat\Partials\CharacteristicForGame;
use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Tables\Tables;
use Granam\Tools\ValueDescriber;

/**
 * @method Fight add(int | \Granam\Integer\IntegerInterface $value)
 * @method Fight sub(int | \Granam\Integer\IntegerInterface $value)
 */
class Fight extends CharacteristicForGame
{

    /**
     * @param ProfessionCode $professionCode
     * @param BaseProperties $baseProperties
     * @param Height $height
     * @param Tables $tables
     * @return Fight
     * @throws \DrdPlus\Properties\Combat\Exceptions\UnknownProfession
     */
    public static function getIt(
        ProfessionCode $professionCode,
        BaseProperties $baseProperties,
        Height $height,
        Tables $tables
    ): Fight
    {
        $fightValue = self::getFightNumberByProfession($professionCode, $baseProperties);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $fightValue += $tables->getCorrectionByHeightTable()->getCorrectionByHeight($height);

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static($fightValue);
    }

    /**
     * See PPH page 34 left column, @link https://pph.drdplus.info/#tabulka_boje
     *
     * @param ProfessionCode $professionCode
     * @param BaseProperties $baseProperties
     * @return int
     * @throws \DrdPlus\Properties\Combat\Exceptions\UnknownProfession
     */
    private static function getFightNumberByProfession(ProfessionCode $professionCode, BaseProperties $baseProperties): int
    {
        switch ($professionCode->getValue()) {
            case ProfessionCode::FIGHTER :
                return $baseProperties->getAgility()->getValue();
            case ProfessionCode::THIEF :
            case ProfessionCode::RANGER : // same as a thief
                return SumAndRound::average($baseProperties->getAgility()->getValue(), $baseProperties->getKnack()->getValue());
            case ProfessionCode::WIZARD :
            case ProfessionCode::THEURGIST : // same as a wizard
                return SumAndRound::average($baseProperties->getAgility()->getValue(), $baseProperties->getIntelligence()->getValue());
            case ProfessionCode::PRIEST :
                return SumAndRound::average($baseProperties->getAgility()->getValue(), $baseProperties->getCharisma()->getValue());
            case ProfessionCode::COMMONER :
                return 0;
            default :
                throw new Exceptions\UnknownProfession(
                    'Unknown profession of code ' . ValueDescriber::describe($professionCode->getValue())
                );
        }
    }

    /**
     * @return CharacteristicForGameCode
     */
    public function getCode(): CharacteristicForGameCode
    {
        return CharacteristicForGameCode::getIt(CharacteristicForGameCode::FIGHT);
    }
}