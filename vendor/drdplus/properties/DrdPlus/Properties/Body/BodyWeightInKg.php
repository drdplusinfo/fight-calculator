<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Body;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\Partials\AbstractFloatProperty;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Measurements\Weight\WeightBonus;
use DrdPlus\Tables\Tables;
use Granam\Number\NumberInterface;

/**
 * Should be equal to @see \DrdPlus\Tables\Measurements\Weight\Weight with kg as unit
 * @method static BodyWeightInKg getIt(float | NumberInterface $value)
 */
class BodyWeightInKg extends AbstractFloatProperty implements BodyProperty
{
    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::BODY_WEIGHT_IN_KG);
    }

    public static function getItByWeight(Weight $weight): BodyWeightInKg
    {
        return self::getIt($weight->getKilograms());
    }

    public function getBodyWeight(Tables $tables): BodyWeight
    {
        return BodyWeight::getIt($this->getWeight($tables));
    }

    public function getWeight(Tables $tables): Weight
    {
        static $weight;
        if ($weight === null) {
            $weight = new Weight($this->getValue(), Weight::KG, $tables->getWeightTable());
        }

        return $weight;
    }

    public function getWeightBonus(Tables $tables): WeightBonus
    {
        return $this->getWeight($tables)->getBonus();
    }

}