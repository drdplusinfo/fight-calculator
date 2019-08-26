<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Dwarfs;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Measurements\Weight\WeightTable;
use DrdPlus\Tests\Races\RaceTest;

abstract class DwarfTest extends RaceTest
{
    /**
     * @param string $propertyCode
     * @param string $genderCode
     * @return string|int|float|bool
     */
    protected function getExpectedOtherProperty($propertyCode, $genderCode)
    {
        $properties = [
            PropertyCode::SENSES => -1,
            PropertyCode::TOUGHNESS => 1,
            PropertyCode::SIZE => 0,
            PropertyCode::BODY_WEIGHT => (new Weight(70, Weight::KG, new WeightTable()))->getValue(),
            PropertyCode::BODY_WEIGHT_IN_KG => 70.0,
            PropertyCode::HEIGHT_IN_CM => 140.0,
            PropertyCode::HEIGHT => 3,
            PropertyCode::INFRAVISION => true,
            PropertyCode::NATIVE_REGENERATION => false,
            PropertyCode::REQUIRES_DM_AGREEMENT => false,
            PropertyCode::REMARKABLE_SENSE => PropertyCode::TOUCH,
        ];

        return $properties[$propertyCode];
    }
}