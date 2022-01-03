<?php declare(strict_types=1);

namespace DrdPlus\PropertiesByFate;

use DrdPlus\Codes\History\ChoiceCode;
use DrdPlus\Codes\History\FateCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use Granam\Strict\Object\StrictObject;

abstract class PropertiesByFate extends StrictObject
{

    /**
     * @var Strength
     */
    private $strength;
    /**
     * @var Agility
     */
    private $agility;
    /**
     * @var Knack
     */
    private $knack;
    /**
     * @var Will
     */
    private $will;
    /**
     * @var Intelligence
     */
    private $intelligence;
    /**
     * @var Charisma
     */
    private $charisma;
    /**
     * @var FateCode
     */
    private $fateCode;

    protected function __construct(
        Strength $strength,
        Agility $agility,
        Knack $knack,
        Will $will,
        Intelligence $intelligence,
        Charisma $charisma,
        FateCode $fateCode
    )
    {
        $this->strength = $strength;
        $this->agility = $agility;
        $this->knack = $knack;
        $this->will = $will;
        $this->intelligence = $intelligence;
        $this->charisma = $charisma;
        $this->fateCode = $fateCode;
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

    public function getFateCode(): FateCode
    {
        return $this->fateCode;
    }

    abstract public function getChoiceCode(): ChoiceCode;

    /**
     * @param PropertyCode $propertyCode
     * @return BaseProperty
     * @throws \DrdPlus\PropertiesByFate\Exceptions\NotFateAffectedProperty
     */
    public function getProperty(PropertyCode $propertyCode): BaseProperty
    {
        switch ($propertyCode->getValue()) {
            case PropertyCode::STRENGTH :
                return $this->getStrength();
            case PropertyCode::AGILITY :
                return $this->getAgility();
            case PropertyCode::KNACK :
                return $this->getKnack();
            case PropertyCode::WILL :
                return $this->getWill();
            case PropertyCode::INTELLIGENCE :
                return $this->getIntelligence();
            case PropertyCode::CHARISMA :
                return $this->getCharisma();
            default :
                throw new Exceptions\NotFateAffectedProperty(
                    "Required property {$propertyCode} is not affected by fate"
                );
        }
    }
}
