<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Body;

use DrdPlus\Codes\Units\DistanceUnitCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Properties\HeightInterface;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerInterface;
use Granam\Strict\Object\StrictObject;

/**
 * In fact bonus of a distance, @see \DrdPlus\Tables\Measurements\Distance\DistanceBonus
 */
class Height extends StrictObject implements BodyProperty, IntegerInterface, HeightInterface
{
    /** @var int */
    private $value;
    /** @var HeightInCm */
    private $heightInCm;

    public static function getIt(HeightInCm $heightInCm, Tables $tables): Height
    {
        return new static($heightInCm, $tables);
    }

    private function __construct(HeightInCm $heightInCm, Tables $tables)
    {
        $this->heightInCm = $heightInCm;
        $heightInDecimeters = $heightInCm->getValue() / 10;
        $distance = new Distance($heightInDecimeters, DistanceUnitCode::DECIMETER, $tables->getDistanceTable());
        // height is bonus of distance in fact
        $this->value = $distance->getBonus()->getValue();
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::HEIGHT);
    }

    public function getHeightInCm(): HeightInCm
    {
        return $this->heightInCm;
    }

    /**
     * It is bonus of distance in fact
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }

}