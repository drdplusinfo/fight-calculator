<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\CalculatorSkeleton\History;
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

class PreviousProperties extends StrictObject
{
    /** @var History */
    private $history;

    public function __construct(History $history)
    {
        $this->history = $history;
    }

    public function getPreviousStrength(): Strength
    {
        return Strength::getIt((int)$this->history->getValue(PropertyCode::STRENGTH));
    }

    public function getPreviousAgility(): Agility
    {
        return Agility::getIt((int)$this->history->getValue(PropertyCode::AGILITY));
    }

    public function getPreviousKnack(): Knack
    {
        return Knack::getIt((int)$this->history->getValue(PropertyCode::KNACK));
    }

    public function getPreviousWill(): Will
    {
        return Will::getIt((int)$this->history->getValue(PropertyCode::WILL));
    }

    public function getPreviousIntelligence(): Intelligence
    {
        return Intelligence::getIt((int)$this->history->getValue(PropertyCode::INTELLIGENCE));
    }

    public function getPreviousCharisma(): Charisma
    {
        return Charisma::getIt((int)$this->history->getValue(PropertyCode::CHARISMA));
    }

    public function getPreviousSize(): Size
    {
        return Size::getIt((int)$this->history->getValue(PropertyCode::SIZE));
    }

    public function getPreviousHeightInCm(): HeightInCm
    {
        return HeightInCm::getIt($this->history->getValue(PropertyCode::HEIGHT_IN_CM) ?? 150);
    }

}