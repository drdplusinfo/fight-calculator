<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace DrdPlus\PropertiesByLevels;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Body\Age;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Properties\Body\BodyWeightInKg;
use DrdPlus\PropertiesByFate\PropertiesByFate;
use DrdPlus\Races\Race;
use DrdPlus\Tables\Tables;
use Granam\Strict\Object\StrictObject;

class FirstLevelProperties extends StrictObject
{
    const INITIAL_PROPERTY_INCREASE_LIMIT = 3;

    /** @var PropertiesByFate */
    private $propertiesByFate;
    /** @var Strength */
    private $firstLevelUnlimitedStrength;
    /** @var Strength */
    private $firstLevelStrength;
    /** @var Agility */
    private $firstLevelUnlimitedAgility;
    /** @var Agility */
    private $firstLevelAgility;
    /** @var Knack */
    private $firstLevelUnlimitedKnack;
    /** @var Knack */
    private $firstLevelKnack;
    /** @var Will */
    private $firstLevelUnlimitedWill;
    /** @var Will */
    private $firstLevelWill;
    /** @var Intelligence */
    private $firstLevelUnlimitedIntelligence;
    /** @var Intelligence */
    private $firstLevelIntelligence;
    /** @var Charisma */
    private $firstLevelUnlimitedCharisma;
    /** @var Charisma */
    private $firstLevelCharisma;
    /** @var BodyWeightInKg */
    private $firstLevelWeightInKgAdjustment;
    /** @var BodyWeightInKg */
    private $firstLevelWeightInKg;
    /** @var Size */
    private $firstLevelSize;
    /** @var HeightInCm */
    private $firstLevelHeightInCmAdjustment;
    /** @var HeightInCm */
    private $firstLevelHeightInCm;
    /** @var Height */
    private $firstLevelHeight;
    /** @var Age */
    private $firstLevelAge;

    /**
     * @param Race $race
     * @param GenderCode $genderCode
     * @param PropertiesByFate $propertiesByFate
     * @param ProfessionLevels $professionLevels
     * @param BodyWeightInKg $weightInKgAdjustment
     * @param HeightInCm $heightInCmAdjustment
     * @param Age $age
     * @param Tables $tables
     * @throws Exceptions\TooLowStrengthAdjustment
     */
    public function __construct(
        Race $race,
        GenderCode $genderCode,
        PropertiesByFate $propertiesByFate,
        ProfessionLevels $professionLevels,
        BodyWeightInKg $weightInKgAdjustment,
        HeightInCm $heightInCmAdjustment,
        Age $age,
        Tables $tables
    )
    {
        $this->propertiesByFate = $propertiesByFate;
        $this->setUpBaseProperties($race, $genderCode, $propertiesByFate, $professionLevels, $tables);
        $this->firstLevelWeightInKgAdjustment = $weightInKgAdjustment;
        $this->firstLevelWeightInKg = $this->createFirstLevelBodyWeightInKg(
            $race,
            $genderCode,
            $weightInKgAdjustment,
            $tables
        );
        $this->firstLevelSize = $this->createFirstLevelSize(
            $race,
            $genderCode,
            $tables,
            $propertiesByFate,
            $professionLevels
        );
        $this->firstLevelHeightInCmAdjustment = $heightInCmAdjustment;
        $this->firstLevelHeightInCm = HeightInCm::getIt(
            $race->getHeightInCm($tables) + $heightInCmAdjustment->getValue()
        );
        $this->firstLevelHeight = Height::getIt($this->firstLevelHeightInCm, $tables);
        $this->firstLevelAge = $age;
    }

    /**
     * @param Race $race
     * @param GenderCode $genderCode
     * @param PropertiesByFate $propertiesByFate
     * @param ProfessionLevels $professionLevels
     * @param Tables $tables
     */
    private function setUpBaseProperties(
        Race $race,
        GenderCode $genderCode,
        PropertiesByFate $propertiesByFate,
        ProfessionLevels $professionLevels,
        Tables $tables
    )
    {
        $propertyValues = [];
        foreach (PropertyCode::getBasePropertyPossibleValues() as $basePropertyCode) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $propertyValues[$basePropertyCode] = $this->calculateFirstLevelBaseProperty(
                PropertyCode::getIt($basePropertyCode),
                $race,
                $genderCode,
                $tables,
                $propertiesByFate,
                $professionLevels
            );
        }

        $this->firstLevelUnlimitedStrength = Strength::getIt($propertyValues[PropertyCode::STRENGTH]);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $this->firstLevelStrength = $this->getLimitedProperty($race, $genderCode, $tables, $this->firstLevelUnlimitedStrength);

        $this->firstLevelUnlimitedAgility = Agility::getIt($propertyValues[PropertyCode::AGILITY]);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $this->firstLevelAgility = $this->getLimitedProperty($race, $genderCode, $tables, $this->firstLevelUnlimitedAgility);

        $this->firstLevelUnlimitedKnack = Knack::getIt($propertyValues[PropertyCode::KNACK]);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $this->firstLevelKnack = $this->getLimitedProperty($race, $genderCode, $tables, $this->firstLevelUnlimitedKnack);

        $this->firstLevelUnlimitedWill = Will::getIt($propertyValues[PropertyCode::WILL]);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $this->firstLevelWill = $this->getLimitedProperty($race, $genderCode, $tables, $this->firstLevelUnlimitedWill);

        $this->firstLevelUnlimitedIntelligence = Intelligence::getIt($propertyValues[PropertyCode::INTELLIGENCE]);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $this->firstLevelIntelligence = $this->getLimitedProperty($race, $genderCode, $tables, $this->firstLevelUnlimitedIntelligence);

        $this->firstLevelUnlimitedCharisma = Charisma::getIt($propertyValues[PropertyCode::CHARISMA]);
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $this->firstLevelCharisma = $this->getLimitedProperty($race, $genderCode, $tables, $this->firstLevelUnlimitedCharisma);
    }

    /**
     * @param PropertyCode $propertyCode
     * @param Race $race
     * @param GenderCode $genderCode
     * @param Tables $tables
     * @param PropertiesByFate $propertiesByFate
     * @param ProfessionLevels $professionLevels
     * @return int
     * @throws \DrdPlus\Races\Exceptions\UnknownPropertyCode
     */
    private function calculateFirstLevelBaseProperty(
        PropertyCode $propertyCode,
        Race $race,
        GenderCode $genderCode,
        Tables $tables,
        PropertiesByFate $propertiesByFate,
        ProfessionLevels $professionLevels
    ): int
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return
            $race->getProperty($propertyCode, $genderCode, $tables)
            + $propertiesByFate->getProperty($propertyCode)->getValue()
            + $professionLevels->getFirstLevelPropertyModifier($propertyCode);
    }

    /**
     * @param Race $race
     * @param GenderCode $genderCode
     * @param Tables $tables
     * @param BaseProperty $baseProperty
     * @return BaseProperty
     * @throws \DrdPlus\Races\Exceptions\UnknownPropertyCode
     */
    private function getLimitedProperty(Race $race, GenderCode $genderCode, Tables $tables, BaseProperty $baseProperty): BaseProperty
    {
        $limit = $this->getBasePropertyLimit($race, $genderCode, $tables, $baseProperty);
        if ($baseProperty->getValue() <= $limit) {
            return $baseProperty;
        }

        return $baseProperty::getIt($limit);
    }

    /**
     * @param Race $race
     * @param GenderCode $genderCode
     * @param Tables $tables
     * @param BaseProperty $baseProperty
     * @return int
     * @throws \DrdPlus\Races\Exceptions\UnknownPropertyCode
     */
    private function getBasePropertyLimit(Race $race, GenderCode $genderCode, Tables $tables, BaseProperty $baseProperty): int
    {
        return $race->getProperty($baseProperty->getCode(), $genderCode, $tables) + self::INITIAL_PROPERTY_INCREASE_LIMIT;
    }

    /**
     * @return PropertiesByFate
     */
    public function getPropertiesByFate(): PropertiesByFate
    {
        return $this->propertiesByFate;
    }

    /**
     * @return int 0+
     */
    public function getStrengthLossBecauseOfLimit(): int
    {
        return $this->firstLevelUnlimitedStrength->getValue() - $this->getFirstLevelStrength()->getValue();
    }

    /**
     * @return int 0+
     */
    public function getAgilityLossBecauseOfLimit(): int
    {
        return $this->firstLevelUnlimitedAgility->getValue() - $this->getFirstLevelAgility()->getValue();
    }

    /**
     * @return int 0+
     */
    public function getKnackLossBecauseOfLimit(): int
    {
        return $this->firstLevelUnlimitedKnack->getValue() - $this->getFirstLevelKnack()->getValue();
    }

    /**
     * @return int 0+
     */
    public function getWillLossBecauseOfLimit(): int
    {
        return $this->firstLevelUnlimitedWill->getValue() - $this->getFirstLevelWill()->getValue();
    }

    /**
     * @return int 0+
     */
    public function getIntelligenceLossBecauseOfLimit(): int
    {
        return $this->firstLevelUnlimitedIntelligence->getValue() - $this->getFirstLevelIntelligence()->getValue();
    }

    /**
     * @return int 0+
     */
    public function getCharismaLossBecauseOfLimit(): int
    {
        return $this->firstLevelUnlimitedCharisma->getValue() - $this->getFirstLevelCharisma()->getValue();
    }

    /**
     * @param Race $race
     * @param GenderCode $genderCode
     * @param BodyWeightInKg $weightInKgAdjustment
     * @param Tables $tables
     * @return BodyWeightInKg
     */
    private function createFirstLevelBodyWeightInKg(
        Race $race,
        GenderCode $genderCode,
        BodyWeightInKg $weightInKgAdjustment,
        Tables $tables
    ): BodyWeightInKg
    {
        return BodyWeightInKg::getIt($race->getWeightInKg($genderCode, $tables) + $weightInKgAdjustment->getValue());
    }

    /**
     * @param Race $race
     * @param GenderCode $genderCode
     * @param Tables $tables
     * @param PropertiesByFate $propertiesByFate
     * @param ProfessionLevels $professionLevels
     * @return Size
     * @throws Exceptions\TooLowStrengthAdjustment
     */
    private function createFirstLevelSize(
        Race $race,
        GenderCode $genderCode,
        Tables $tables,
        PropertiesByFate $propertiesByFate,
        ProfessionLevels $professionLevels
    ): Size
    {
        // the race bonus is NOT count for adjustment, doesn't count to size change respectively
        $sizeModifierByStrength = $this->getSizeModifierByStrength(
            $propertiesByFate->getStrength()->getValue()
            + $professionLevels->getFirstLevelStrengthModifier()
        );
        $raceSize = $race->getSize($genderCode, $tables);

        return Size::getIt($raceSize + $sizeModifierByStrength);
    }

    /**
     * @param $firstLevelStrengthAdjustment
     * @return int
     * @throws Exceptions\TooLowStrengthAdjustment
     */
    private function getSizeModifierByStrength($firstLevelStrengthAdjustment): int
    {
        if ($firstLevelStrengthAdjustment === 0) {
            return -1;
        }
        if ($firstLevelStrengthAdjustment === 1) {
            return 0;
        }
        if ($firstLevelStrengthAdjustment >= 2) {
            return +1;
        }
        throw new Exceptions\TooLowStrengthAdjustment(
            'First level strength adjustment can not be lesser than zero. Given ' . $firstLevelStrengthAdjustment
        );
    }

    public function getFirstLevelStrength(): Strength
    {
        return $this->firstLevelStrength;
    }

    public function getFirstLevelAgility(): Agility
    {
        return $this->firstLevelAgility;
    }

    public function getFirstLevelKnack(): Knack
    {
        return $this->firstLevelKnack;
    }

    public function getFirstLevelWill(): Will
    {
        return $this->firstLevelWill;
    }

    public function getFirstLevelIntelligence(): Intelligence
    {
        return $this->firstLevelIntelligence;
    }

    public function getFirstLevelCharisma(): Charisma
    {
        return $this->firstLevelCharisma;
    }

    public function getFirstLevelBodyWeightInKgAdjustment(): BodyWeightInKg
    {
        return $this->firstLevelWeightInKgAdjustment;
    }

    public function getFirstLevelWeightInKg(): BodyWeightInKg
    {
        return $this->firstLevelWeightInKg;
    }

    public function getFirstLevelSize(): Size
    {
        return $this->firstLevelSize;
    }

    public function getFirstLevelHeightInCmAdjustment(): HeightInCm
    {
        return $this->firstLevelHeightInCmAdjustment;
    }

    public function getFirstLevelHeightInCm(): HeightInCm
    {
        return $this->firstLevelHeightInCm;
    }

    public function getFirstLevelHeight(): Height
    {
        return $this->firstLevelHeight;
    }

    public function getFirstLevelAge(): Age
    {
        return $this->firstLevelAge;
    }

}