<?php declare(strict_types=1);

namespace DrdPlus\PropertiesByLevels;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\BaseProperties\Agility;
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
use DrdPlus\Properties\Combat\Attack;
use DrdPlus\Properties\Combat\BaseProperties;
use DrdPlus\Properties\Combat\Defense;
use DrdPlus\Properties\Combat\DefenseNumber;
use DrdPlus\Properties\Combat\Fight;
use DrdPlus\Properties\Combat\Shooting;
use DrdPlus\Properties\Derived\Beauty;
use DrdPlus\Properties\Derived\Dangerousness;
use DrdPlus\Properties\Derived\Dignity;
use DrdPlus\Properties\Derived\Endurance;
use DrdPlus\Properties\Derived\FatigueBoundary;
use DrdPlus\Properties\Derived\Senses;
use DrdPlus\Properties\Derived\Speed;
use DrdPlus\Properties\Derived\Toughness;
use DrdPlus\Properties\Derived\WoundBoundary;
use DrdPlus\PropertiesByFate\PropertiesByFate;
use DrdPlus\Races\Race;
use DrdPlus\Tables\Tables;
use Granam\Strict\Object\StrictObject;

class PropertiesByLevels extends StrictObject implements BaseProperties
{

    /** @var FirstLevelProperties */
    private $firstLevelProperties;
    /** @var NextLevelsProperties */
    private $nextLevelsProperties;
    /** @var Strength */
    private $strength;
    /** @var Agility */
    private $agility;
    /** @var Knack */
    private $knack;
    /** @var Will */
    private $will;
    /** @var Intelligence */
    private $intelligence;
    /** @var Charisma */
    private $charisma;
    /** @var Toughness */
    private $toughness;
    /** @var Endurance */
    private $endurance;
    /** @var Speed */
    private $speed;
    /** @var Senses */
    private $senses;
    /** @var Beauty */
    private $beauty;
    /** @var Dangerousness */
    private $dangerousness;
    /** @var Dignity */
    private $dignity;
    /** @var Fight */
    private $fight;
    /** @var Attack */
    private $attack;
    /** @var Shooting */
    private $shooting;
    /** @var DefenseNumber */
    private $defense;
    /** @var WoundBoundary */
    private $woundsLimit;
    /** @var FatigueBoundary */
    private $fatigueLimit;

    /**
     * @param Race $race
     * @param GenderCode $genderCode
     * @param PropertiesByFate $propertiesByFate
     * @param ProfessionLevels $professionLevels
     * @param BodyWeightInKg $weightInKgAdjustment
     * @param HeightInCm $heightInCm
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
        HeightInCm $heightInCm,
        Age $age,
        Tables $tables
    )
    {
        $this->firstLevelProperties = new FirstLevelProperties(
            $race,
            $genderCode,
            $propertiesByFate,
            $professionLevels,
            $weightInKgAdjustment,
            $heightInCm,
            $age,
            $tables
        );
        $this->nextLevelsProperties = new NextLevelsProperties($professionLevels);

        $this->strength = Strength::getIt(
            $this->firstLevelProperties->getFirstLevelStrength()->getValue()
            + $this->nextLevelsProperties->getNextLevelsStrength()->getValue()
        );
        $this->agility = Agility::getIt(
            $this->firstLevelProperties->getFirstLevelAgility()->getValue()
            + $this->nextLevelsProperties->getNextLevelsAgility()->getValue()
        );
        $this->knack = Knack::getIt(
            $this->firstLevelProperties->getFirstLevelKnack()->getValue()
            + $this->nextLevelsProperties->getNextLevelsKnack()->getValue()
        );
        $this->will = Will::getIt(
            $this->firstLevelProperties->getFirstLevelWill()->getValue()
            + $this->nextLevelsProperties->getNextLevelsWill()->getValue()
        );
        $this->intelligence = Intelligence::getIt(
            $this->firstLevelProperties->getFirstLevelIntelligence()->getValue()
            + $this->nextLevelsProperties->getNextLevelsIntelligence()->getValue()
        );
        $this->charisma = Charisma::getIt(
            $this->firstLevelProperties->getFirstLevelCharisma()->getValue()
            + $this->nextLevelsProperties->getNextLevelsCharisma()->getValue()
        );

        // delivered properties
        $this->toughness = Toughness::getIt(
            $this->getStrength(), $race->getRaceCode(), $race->getSubraceCode(), $tables
        );
        $this->endurance = Endurance::getIt($this->getStrength(), $this->getWill());
        $this->speed = Speed::getIt($this->getStrength(), $this->getAgility(), $this->getHeight());
        $this->senses = Senses::getIt(
            $this->getKnack(),
            RaceCode::getIt($race->getRaceCode()),
            SubRaceCode::getIt($race->getSubraceCode()),
            $tables
        );
        // aspects of visage
        $this->beauty = Beauty::getIt($this->getAgility(), $this->getKnack(), $this->getCharisma());
        $this->dangerousness = Dangerousness::getIt($this->getStrength(), $this->getWill(), $this->getCharisma());
        $this->dignity = Dignity::getIt($this->getIntelligence(), $this->getWill(), $this->getCharisma());

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $this->fight = Fight::getIt(
            $professionLevels->getFirstLevel()->getProfession()->getCode(),
            $this,
            $this->getHeight(),
            $tables
        );
        $this->attack = Attack::getIt($this->getAgility());
        $this->shooting = Shooting::getIt($this->getKnack());
        $this->defense = Defense::getIt($this->getAgility());

        $this->woundsLimit = WoundBoundary::getIt($this->getToughness(), $tables);
        $this->fatigueLimit = FatigueBoundary::getIt($this->getEndurance(), $tables);
    }

    public function getFirstLevelProperties(): FirstLevelProperties
    {
        return $this->firstLevelProperties;
    }

    public function getNextLevelsProperties(): NextLevelsProperties
    {
        return $this->nextLevelsProperties;
    }

    public function getStrength(): Strength
    {
        return $this->strength;
    }

    public function getAgility(): Agility
    {
        return $this->agility;
    }

    public function getKnack(): Knack
    {
        return $this->knack;
    }

    public function getWill(): Will
    {
        return $this->will;
    }

    public function getIntelligence(): Intelligence
    {
        return $this->intelligence;
    }

    public function getCharisma(): Charisma
    {
        return $this->charisma;
    }

    public function getBodyWeightInKgAdjustment(): BodyWeightInKg
    {
        // there is no more weight adjustments than on first level
        return $this->firstLevelProperties->getFirstLevelBodyWeightInKgAdjustment();
    }

    public function getWeightInKg(): BodyWeightInKg
    {
        // there is no more weight adjustments than on first level
        return $this->firstLevelProperties->getFirstLevelWeightInKg();
    }

    public function getHeightInCmAdjustment(): HeightInCm
    {
        // there is no more height adjustments than on first level
        return $this->firstLevelProperties->getFirstLevelHeightInCmAdjustment();
    }

    public function getHeightInCm(): HeightInCm
    {
        // there is no more height adjustments than on first level
        return $this->firstLevelProperties->getFirstLevelHeightInCm();
    }

    public function getHeight(): Height
    {
        // there is no more height adjustments than on first level
        return $this->firstLevelProperties->getFirstLevelHeight();
    }

    public function getAge(): Age
    {
        // there is no more age adjustments than on first level (yet)
        return $this->firstLevelProperties->getFirstLevelAge();
    }

    public function getToughness(): Toughness
    {
        return $this->toughness;
    }

    public function getEndurance(): Endurance
    {
        return $this->endurance;
    }

    public function getSize(): Size
    {
        return $this->firstLevelProperties->getFirstLevelSize();
    }

    public function getSpeed(): Speed
    {
        return $this->speed;
    }

    public function getSenses(): Senses
    {
        return $this->senses;
    }

    public function getBeauty(): Beauty
    {
        return $this->beauty;
    }

    public function getDangerousness(): Dangerousness
    {
        return $this->dangerousness;
    }

    public function getDignity(): Dignity
    {
        return $this->dignity;
    }

    public function getFight(): Fight
    {
        return $this->fight;
    }

    public function getAttack(): Attack
    {
        return $this->attack;
    }

    public function getShooting(): Shooting
    {
        return $this->shooting;
    }

    public function getDefense(): Defense
    {
        return $this->defense;
    }

    public function getWoundBoundary(): WoundBoundary
    {
        return $this->woundsLimit;
    }

    public function getFatigueBoundary(): FatigueBoundary
    {
        return $this->fatigueLimit;
    }

}