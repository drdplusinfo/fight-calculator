<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Orcs;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;

class GoblinTest extends OrcTest
{
    protected function getExpectedBaseProperty($genderCode, $propertyCode)
    {
        $properties = [
            PropertyCode::STRENGTH => [
                GenderCode::MALE => -1,
                GenderCode::FEMALE => -2,
            ],
            PropertyCode::AGILITY => 2,
            PropertyCode::KNACK => 1,
            PropertyCode::WILL => [
                GenderCode::MALE => -2,
                GenderCode::FEMALE => -1,
            ],
            PropertyCode::INTELLIGENCE => 0,
            PropertyCode::CHARISMA => -1,
        ];


        return isset($properties[$propertyCode][$genderCode])
            ? $properties[$propertyCode][$genderCode]
            : $properties[$propertyCode];
    }

    protected function getExpectedOtherProperty($propertyCode, $genderCode)
    {
        $properties = [
            PropertyCode::SIZE => [
                GenderCode::MALE => -1,
                GenderCode::FEMALE => -2
            ],
            PropertyCode::BODY_WEIGHT_IN_KG => [
                GenderCode::MALE => 55.0,
                GenderCode::FEMALE => 50.0,
            ],
            PropertyCode::HEIGHT_IN_CM => 150.0,
            PropertyCode::HEIGHT => 4, // closest lower bonus to distance of 1.5 meters
            PropertyCode::AGE => 9,
        ];


        if (isset($properties[$propertyCode][$genderCode])) {
            return $properties[$propertyCode][$genderCode];
        }
        if (isset($properties[$propertyCode])) {
            return $properties[$propertyCode];
        }

        return parent::getExpectedOtherProperty($propertyCode, $genderCode);
    }
}