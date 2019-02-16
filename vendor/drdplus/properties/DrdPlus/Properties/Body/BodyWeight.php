<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Body;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Measurements\Weight\WeightBonus;
use DrdPlus\Tables\Properties\BodyWeightInterface;
use Granam\Integer\IntegerInterface;
use Granam\Strict\Object\StrictObject;

/**
 * In fact bonus of weight in kg minus 12, see @link https://pph.drdplus.info/#vypocet_hmotnosti_postavy
 * and @see \DrdPlus\Tables\Measurements\Weight\WeightBonus
 * @method BodyWeight add(int | IntegerInterface $value)
 * @method BodyWeight sub(int | IntegerInterface $value)
 */
class BodyWeight extends StrictObject implements BodyProperty, IntegerInterface, BodyWeightInterface
{
    /**
     * @param Weight $weight
     * @return BodyWeight
     */
    public static function getIt(Weight $weight): BodyWeight
    {
        return new static($weight);
    }

    /** @var int */
    private $value;
    /** @var Weight */
    private $weight;

    /**
     * @param Weight $weight
     */
    private function __construct(Weight $weight)
    {
        /** @link https://pph.drdplus.info/#vypocet_hmotnosti_postavy */
        $this->value = $weight->getBonus()->getValue() - 12;
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::BODY_WEIGHT);
    }

    /**
     * @return Weight
     */
    public function getWeight(): Weight
    {
        return $this->weight;
    }

    /**
     * @return WeightBonus
     */
    public function getWeightBonus(): WeightBonus
    {
        return $this->weight->getBonus();
    }

    public function getBodyWeightInKg(): BodyWeightInKg
    {
        return BodyWeightInKg::getIt($this->weight->getKilograms());
    }
}