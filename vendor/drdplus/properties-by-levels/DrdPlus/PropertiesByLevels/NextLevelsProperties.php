<?php declare(strict_types=1);

namespace DrdPlus\PropertiesByLevels;

use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use Granam\Strict\Object\StrictObject;

class NextLevelsProperties extends StrictObject
{
    /** @var Strength */
    private $nextLevelsStrength;
    /** @var Agility */
    private $nextLevelsAgility;
    /** @var Knack */
    private $nextLevelsKnack;
    /** @var Will */
    private $nextLevelsWill;
    /** @var Intelligence */
    private $nextLevelsIntelligence;
    /** @var Charisma */
    private $nextLevelsCharisma;

    /**
     * @param ProfessionLevels $professionLevels
     */
    public function __construct(ProfessionLevels $professionLevels)
    {
        $this->nextLevelsStrength = Strength::getIt($professionLevels->getNextLevelsStrengthModifier());
        $this->nextLevelsAgility = Agility::getIt($professionLevels->getNextLevelsAgilityModifier());
        $this->nextLevelsKnack = Knack::getIt($professionLevels->getNextLevelsKnackModifier());
        $this->nextLevelsWill = Will::getIt($professionLevels->getNextLevelsWillModifier());
        $this->nextLevelsIntelligence = Intelligence::getIt($professionLevels->getNextLevelsIntelligenceModifier());
        $this->nextLevelsCharisma = Charisma::getIt($professionLevels->getNextLevelsCharismaModifier());
    }

    /**
     * @return Strength
     */
    public function getNextLevelsStrength()
    {
        return $this->nextLevelsStrength;
    }

    /**
     * @return Agility
     */
    public function getNextLevelsAgility()
    {
        return $this->nextLevelsAgility;
    }

    /**
     * @return Knack
     */
    public function getNextLevelsKnack()
    {
        return $this->nextLevelsKnack;
    }

    /**
     * @return Will
     */
    public function getNextLevelsWill()
    {
        return $this->nextLevelsWill;
    }

    /**
     * @return Intelligence
     */
    public function getNextLevelsIntelligence()
    {
        return $this->nextLevelsIntelligence;
    }

    /**
     * @return Charisma
     */
    public function getNextLevelsCharisma()
    {
        return $this->nextLevelsCharisma;
    }
}
