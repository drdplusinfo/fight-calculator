<?php declare(strict_types=1);

namespace DrdPlus\Races;

use DrdPlus\Codes\Units\DistanceUnitCode;
use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Tables;
use Granam\ScalarEnum\ScalarEnum;
use Granam\String\StringInterface;
use Granam\Tools\ValueDescriber;

/**
 * @method static Race getEnum($value)
 */
abstract class Race extends ScalarEnum
{
    /**
     * @param string|StringInterface $value
     * @throws Exceptions\UnknownRaceCode
     */
    protected function __construct($value)
    {
        parent::__construct($value);
        $this->checkRaceEnumValue($this->getValue());
    }

    /**
     * @param string $value
     * @throws \DrdPlus\Races\Exceptions\UnknownRaceCode
     */
    private function checkRaceEnumValue(string $value)
    {
        if ($value !== self::createRaceAndSubRaceCode($this->getRaceCode(), $this->getSubRaceCode())) {
            throw new Exceptions\UnknownRaceCode(
                'Expected ' . self::createRaceAndSubRaceCode($this->getRaceCode(), $this->getSubRaceCode())
                . ' got ' . ValueDescriber::describe($value)
            );
        }
    }

    protected static function getItByRaceAndSubRace(RaceCode $raceCode, SubRaceCode $subRaceCode): Race
    {
        return self::getEnum(self::createRaceAndSubRaceCode($raceCode, $subRaceCode));
    }

    private static function createRaceAndSubRaceCode(RaceCode $raceCode, SubRaceCode $subRaceCode): string
    {
        return "$raceCode-$subRaceCode";
    }

    abstract public function getRaceCode(): RaceCode;

    abstract public function getSubRaceCode(): SubRaceCode;

    public function getStrength(GenderCode $genderCode, Tables $tables): int
    {
        if ($genderCode->isMale()) {
            return $tables->getRacesTable()->getMaleStrength($this->getRaceCode(), $this->getSubRaceCode());
        }
        return $tables->getRacesTable()->getFemaleStrength(
            $this->getRaceCode(), $this->getSubRaceCode(), $tables->getFemaleModifiersTable()
        );
    }

    public function getAgility(GenderCode $genderCode, Tables $tables): int
    {
        if ($genderCode->isMale()) {
            return $tables->getRacesTable()->getMaleAgility($this->getRaceCode(), $this->getSubRaceCode());
        }
        return $tables->getRacesTable()->getFemaleAgility(
            $this->getRaceCode(), $this->getSubRaceCode(), $tables->getFemaleModifiersTable()
        );
    }

    public function getKnack(GenderCode $genderCode, Tables $tables): int
    {
        if ($genderCode->isMale()) {
            return $tables->getRacesTable()->getMaleKnack($this->getRaceCode(), $this->getSubRaceCode());
        }
        return $tables->getRacesTable()->getFemaleKnack($this->getRaceCode(), $this->getSubRaceCode());
    }

    public function getWill(GenderCode $genderCode, Tables $tables): int
    {
        if ($genderCode->isMale()) {
            return $tables->getRacesTable()->getMaleWill($this->getRaceCode(), $this->getSubRaceCode());
        }
        return $tables->getRacesTable()->getFemaleWill($this->getRaceCode(), $this->getSubRaceCode());
    }

    public function getIntelligence(GenderCode $genderCode, Tables $tables): int
    {
        if ($genderCode->isMale()) {
            return $tables->getRacesTable()->getMaleIntelligence($this->getRaceCode(), $this->getSubRaceCode());
        }
        return $tables->getRacesTable()->getFemaleIntelligence(
            $this->getRaceCode(), $this->getSubRaceCode(), $tables->getFemaleModifiersTable()
        );
    }

    public function getCharisma(GenderCode $genderCode, Tables $tables): int
    {
        if ($genderCode->isMale()) {
            return $tables->getRacesTable()->getMaleCharisma($this->getRaceCode(), $this->getSubRaceCode());
        }
        return $tables->getRacesTable()->getFemaleCharisma(
            $this->getRaceCode(), $this->getSubRaceCode(), $tables->getFemaleModifiersTable()
        );
    }

    public function getSenses(Tables $tables): int
    {
        return $tables->getRacesTable()->getSenses($this->getRaceCode(), $this->getSubRaceCode());
    }

    public function getToughness(Tables $tables): int
    {
        return $tables->getRacesTable()->getToughness($this->getRaceCode(), $this->getSubRaceCode());
    }

    public function getSize(GenderCode $genderCode, Tables $tables): int
    {
        return $tables->getRacesTable()->getSize(
            $this->getRaceCode(),
            $this->getSubRaceCode(),
            $genderCode,
            $tables->getFemaleModifiersTable()
        );
    }

    /**
     * Bonus of body weight
     *
     * @param GenderCode $genderCode
     * @param Tables $tables
     * @return int
     */
    public function getBodyWeight(GenderCode $genderCode, Tables $tables): int
    {
        $weightInKg = $this->getWeightInKg($genderCode, $tables);

        return (new Weight($weightInKg, Weight::KG, $tables->getWeightTable()))->getBonus()->getValue();
    }

    public function getWeightInKg(GenderCode $genderCode, Tables $tables): float
    {
        if ($genderCode->isMale()) {
            return $tables->getRacesTable()->getMaleWeightInKg($this->getRaceCode(), $this->getSubRaceCode());
        }

        return $tables->getRacesTable()->getFemaleWeightInKg(
            $this->getRaceCode(),
            $this->getSubRaceCode(),
            $tables->getFemaleModifiersTable(),
            $tables->getWeightTable()
        );
    }

    public function getHeightInCm(Tables $tables): float
    {
        return $tables->getRacesTable()->getHeightInCm($this->getRaceCode(), $this->getSubRaceCode());
    }

    /**
     * Gives race height as bonus of distance (height in cm).
     * Useful for speed and fight modifiers.
     *
     * @param Tables $tables
     * @return int
     */
    public function getHeight(Tables $tables): int
    {
        $heightInMeters = $this->getHeightInCm($tables) / 100;
        $distance = new Distance($heightInMeters, DistanceUnitCode::METER, $tables->getDistanceTable());

        return $distance->getBonus()->getValue();
    }

    public function hasInfravision(Tables $tables): bool
    {
        return $tables->getRacesTable()->hasInfravision($this->getRaceCode(), $this->getSubRaceCode());
    }

    public function hasNativeRegeneration(Tables $tables): bool
    {
        return $tables->getRacesTable()->hasNativeRegeneration($this->getRaceCode(), $this->getSubRaceCode());
    }

    public function requiresDmAgreement(Tables $tables): bool
    {
        return $tables->getRacesTable()->requiresDmAgreement($this->getRaceCode(), $this->getSubRaceCode());
    }

    public function getRemarkableSense(Tables $tables): string
    {
        return $tables->getRacesTable()->getRemarkableSense($this->getRaceCode(), $this->getSubRaceCode());
    }

    /**
     * Gives usual age of a race on his first great adventure - like 15 years for common human or 25 for hobbit.
     *
     * @param Tables $tables
     * @return int
     */
    public function getAge(Tables $tables): int
    {
        return $tables->getRacesTable()->getAge($this->getRaceCode(), $this->getSubRaceCode());
    }

    /**
     * @param PropertyCode $propertyCode
     * @param GenderCode $genderCode
     * @param Tables $tables
     * @return int|float|bool|string
     * @throws \DrdPlus\Races\Exceptions\UnknownPropertyCode
     */
    public function getProperty(PropertyCode $propertyCode, GenderCode $genderCode, Tables $tables)
    {
        switch ($propertyCode->getValue()) {
            case PropertyCode::STRENGTH :
                return $this->getStrength($genderCode, $tables);
            case PropertyCode::AGILITY :
                return $this->getAgility($genderCode, $tables);
            case PropertyCode::KNACK :
                return $this->getKnack($genderCode, $tables);
            case PropertyCode::WILL :
                return $this->getWill($genderCode, $tables);
            case PropertyCode::INTELLIGENCE :
                return $this->getIntelligence($genderCode, $tables);
            case PropertyCode::CHARISMA :
                return $this->getCharisma($genderCode, $tables);
            case PropertyCode::SENSES :
                return $this->getSenses($tables);
            case PropertyCode::TOUGHNESS :
                return $this->getToughness($tables);
            case PropertyCode::SIZE :
                return $this->getSize($genderCode, $tables);
            case PropertyCode::BODY_WEIGHT :
                return $this->getBodyWeight($genderCode, $tables);
            case PropertyCode::BODY_WEIGHT_IN_KG :
                return $this->getWeightInKg($genderCode, $tables);
            case PropertyCode::HEIGHT_IN_CM :
                return $this->getHeightInCm($tables);
            case PropertyCode::HEIGHT :
                return $this->getHeight($tables);
            case PropertyCode::INFRAVISION :
                return $this->hasInfravision($tables);
            case PropertyCode::NATIVE_REGENERATION :
                return $this->hasNativeRegeneration($tables);
            case PropertyCode::REQUIRES_DM_AGREEMENT :
                return $this->requiresDmAgreement($tables);
            case PropertyCode::REMARKABLE_SENSE :
                return $this->getRemarkableSense($tables);
            case PropertyCode::AGE :
                return $this->getAge($tables);
            default :
                throw new Exceptions\UnknownPropertyCode(
                    'Unknown property ' . ValueDescriber::describe($propertyCode)
                );
        }
    }
}