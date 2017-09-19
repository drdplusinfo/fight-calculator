<?php
namespace DrdPlus\Fight;

use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use Granam\Strict\Object\StrictObject;

class PreviousProperties extends StrictObject
{
    /** @var PreviousValues */
    private $previousValues;

    public function __construct(PreviousValues $previousValues)
    {
        $this->previousValues = $previousValues;
    }

    public function getPreviousStrength(): Strength
    {
        return Strength::getIt((int)$this->previousValues->getValue(Controller::STRENGTH));
    }

    public function getPreviousAgility(): Agility
    {
        return Agility::getIt((int)$this->previousValues->getValue(Controller::AGILITY));
    }

    public function getPreviousKnack(): Knack
    {
        return Knack::getIt((int)$this->previousValues->getValue(Controller::KNACK));
    }

    public function getPreviousWill(): Will
    {
        return Will::getIt((int)$this->previousValues->getValue(Controller::WILL));
    }

    public function getPreviousIntelligence(): Intelligence
    {
        return Intelligence::getIt((int)$this->previousValues->getValue(Controller::INTELLIGENCE));
    }

    public function getPreviousCharisma(): Charisma
    {
        return Charisma::getIt((int)$this->previousValues->getValue(Controller::CHARISMA));
    }

    public function getPreviousSize(): Size
    {
        return Size::getIt((int)$this->previousValues->getValue(Controller::SIZE));
    }

    public function getPreviousHeightInCm(): HeightInCm
    {
        return HeightInCm::getIt($this->previousValues->getValue(Controller::HEIGHT_IN_CM) ?? 150);
    }

}