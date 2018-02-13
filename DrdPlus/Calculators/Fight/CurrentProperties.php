<?php
namespace DrdPlus\Calculators\Fight;

use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use Granam\Strict\Object\StrictObject;

class CurrentProperties extends StrictObject
{
    /** @var CurrentValues */
    private $currentValues;

    public function __construct(CurrentValues $currentValues)
    {
        $this->currentValues = $currentValues;
    }

    public function getCurrentStrength(): Strength
    {
        return Strength::getIt((int)$this->currentValues->getValue(Controller::STRENGTH));
    }

    public function getCurrentAgility(): Agility
    {
        return Agility::getIt((int)$this->currentValues->getValue(Controller::AGILITY));
    }

    public function getCurrentKnack(): Knack
    {
        return Knack::getIt((int)$this->currentValues->getValue(Controller::KNACK));
    }

    public function getCurrentWill(): Will
    {
        return Will::getIt((int)$this->currentValues->getValue(Controller::WILL));
    }

    public function getCurrentIntelligence(): Intelligence
    {
        return Intelligence::getIt((int)$this->currentValues->getValue(Controller::INTELLIGENCE));
    }

    public function getCurrentCharisma(): Charisma
    {
        return Charisma::getIt((int)$this->currentValues->getValue(Controller::CHARISMA));
    }

    public function getCurrentSize(): Size
    {
        return Size::getIt((int)$this->currentValues->getValue(Controller::SIZE));
    }

    public function getCurrentHeightInCm(): HeightInCm
    {
        return HeightInCm::getIt($this->currentValues->getValue(Controller::HEIGHT_IN_CM) ?? 150);
    }

}