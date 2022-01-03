<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use Granam\Strict\Object\StrictObject;

class CurrentProperties extends StrictObject
{
    private \DrdPlus\CalculatorSkeleton\CurrentValues $currentValues;

    public function __construct(CurrentValues $currentValues)
    {
        $this->currentValues = $currentValues;
    }

    public function getCurrentStrength(): Strength
    {
        return Strength::getIt((int)$this->currentValues->getCurrentValue(PropertyCode::STRENGTH));
    }

    public function getCurrentAgility(): Agility
    {
        return Agility::getIt((int)$this->currentValues->getCurrentValue(PropertyCode::AGILITY));
    }

    public function getCurrentKnack(): Knack
    {
        return Knack::getIt((int)$this->currentValues->getCurrentValue(PropertyCode::KNACK));
    }

    public function getCurrentWill(): Will
    {
        return Will::getIt((int)$this->currentValues->getCurrentValue(PropertyCode::WILL));
    }

    public function getCurrentIntelligence(): Intelligence
    {
        return Intelligence::getIt((int)$this->currentValues->getCurrentValue(PropertyCode::INTELLIGENCE));
    }

    public function getCurrentCharisma(): Charisma
    {
        return Charisma::getIt((int)$this->currentValues->getCurrentValue(PropertyCode::CHARISMA));
    }

    public function getCurrentSize(): Size
    {
        return Size::getIt((int)$this->currentValues->getCurrentValue(PropertyCode::SIZE));
    }

    public function getCurrentHeightInCm(): HeightInCm
    {
        return HeightInCm::getIt($this->currentValues->getCurrentValue(PropertyCode::HEIGHT_IN_CM) ?? 150);
    }

}