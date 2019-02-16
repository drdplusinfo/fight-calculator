<?php
declare(strict_types=1);

namespace DrdPlus\PropertiesByFate;

use DrdPlus\Codes\History\ChoiceCode;
use DrdPlus\Codes\History\FateCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Professions\Profession;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Tables\Tables;

class ChosenProperties extends PropertiesByFate
{

    /**
     * @param Strength $strength
     * @param Agility $agility
     * @param Knack $knack
     * @param Will $will
     * @param Intelligence $intelligence
     * @param Charisma $charisma
     * @param FateCode $fateCode
     * @param Profession $profession
     * @param Tables $tables
     * @throws \DrdPlus\PropertiesByFate\Exceptions\InvalidValueOfChosenProperty
     * @throws \DrdPlus\PropertiesByFate\Exceptions\InvalidSumOfChosenProperties
     */
    public function __construct(
        Strength $strength,
        Agility $agility,
        Knack $knack,
        Will $will,
        Intelligence $intelligence,
        Charisma $charisma,
        FateCode $fateCode,
        Profession $profession,
        Tables $tables
    )
    {
        $this->checkChosenProperties(
            $strength,
            $agility,
            $knack,
            $will,
            $intelligence,
            $charisma,
            $fateCode,
            $profession,
            $tables
        );
        parent::__construct($strength, $agility, $knack, $will, $intelligence, $charisma, $fateCode);
    }

    /**
     * @param Strength $strength
     * @param Agility $agility
     * @param Knack $knack
     * @param Will $will
     * @param Intelligence $intelligence
     * @param Charisma $charisma
     * @param FateCode $fate
     * @param Profession $profession
     * @param Tables $tables
     * @throws \DrdPlus\PropertiesByFate\Exceptions\InvalidValueOfChosenProperty
     * @throws \DrdPlus\PropertiesByFate\Exceptions\InvalidSumOfChosenProperties
     */
    private function checkChosenProperties(
        Strength $strength,
        Agility $agility,
        Knack $knack,
        Will $will,
        Intelligence $intelligence,
        Charisma $charisma,
        FateCode $fate,
        Profession $profession,
        Tables $tables
    )
    {
        $primaryPropertiesSum = 0;
        $secondaryPropertiesSum = 0;
        foreach ([$strength, $agility, $knack, $will, $intelligence, $charisma] as $property) {
            $this->checkChosenProperty($profession, $fate, $property, $tables);

            /** @var BaseProperty $property */
            if ($profession->isPrimaryProperty(PropertyCode::getIt($property->getCode()))) {
                $primaryPropertiesSum += $property->getValue();
            } else {
                $secondaryPropertiesSum += $property->getValue();
            }
        }

        $this->checkChosenPropertiesSum($primaryPropertiesSum, $secondaryPropertiesSum, $fate, $profession, $tables);
    }

    /**
     * @param Profession $profession
     * @param FateCode $fate
     * @param BaseProperty $chosenProperty
     * @param Tables $tables
     * @throws \DrdPlus\PropertiesByFate\Exceptions\InvalidValueOfChosenProperty
     */
    private function checkChosenProperty(
        Profession $profession,
        FateCode $fate,
        BaseProperty $chosenProperty,
        Tables $tables
    )
    {
        if ($chosenProperty->getValue() > $tables->getPlayerDecisionsTable()->getMaximumToSingleProperty($fate)) {
            throw new Exceptions\InvalidValueOfChosenProperty(
                "Requested {$chosenProperty->getCode()} value {$chosenProperty->getValue()} is higher than allowed"
                . " maximum {$tables->getPlayerDecisionsTable()->getMaximumToSingleProperty($fate)}"
                . " for profession {$profession->getValue()} and fate {$fate}"
            );
        }
    }

    /**
     * @param int $primaryPropertiesSum
     * @param int $secondaryPropertiesSum
     * @param FateCode $fateCode
     * @param Profession $profession
     * @param Tables $tables
     * @throws \DrdPlus\PropertiesByFate\Exceptions\InvalidSumOfChosenProperties
     */
    private function checkChosenPropertiesSum(
        int $primaryPropertiesSum,
        int $secondaryPropertiesSum,
        FateCode $fateCode,
        Profession $profession,
        Tables $tables
    )
    {
        if ($primaryPropertiesSum !== $tables->getPlayerDecisionsTable()->getPointsToPrimaryProperties($fateCode)) {
            throw new Exceptions\InvalidSumOfChosenProperties(
                "Expected {$tables->getPlayerDecisionsTable()->getPointsToPrimaryProperties($fateCode)} as sum of primary properties,"
                . " got $primaryPropertiesSum for profession '{$profession->getValue()}'"
                . " and fate '{$fateCode}'"
            );
        }

        if ($secondaryPropertiesSum !== $tables->getPlayerDecisionsTable()->getPointsToSecondaryProperties($fateCode)) {
            throw new Exceptions\InvalidSumOfChosenProperties(
                "Expected {$tables->getPlayerDecisionsTable()->getPointsToSecondaryProperties($fateCode)} as sum of secondary properties,"
                . " got $secondaryPropertiesSum for profession '{$profession->getValue()}'"
                . " and fate '{$fateCode}'"
            );
        }
    }

    public function getChoiceCode(): ChoiceCode
    {
        return ChoiceCode::getIt(ChoiceCode::PLAYER_DECISION);
    }

}