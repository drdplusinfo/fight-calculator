<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Orcs;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;

class CommonOrcTest extends OrcTest
{
    protected function getExpectedBaseProperty($genderCode, $propertyCode)
    {
        $properties = [
            PropertyCode::STRENGTH => [
                GenderCode::MALE => 0,
                GenderCode::FEMALE => -1,
            ],
            PropertyCode::AGILITY => 2,
            PropertyCode::KNACK => 0,
            PropertyCode::WILL => [
                GenderCode::MALE => -1,
                GenderCode::FEMALE => 0,
            ],
            PropertyCode::INTELLIGENCE => 0,
            PropertyCode::CHARISMA => -2,
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
                GenderCode::FEMALE => -2,
            ],
            PropertyCode::BODY_WEIGHT_IN_KG => [
                GenderCode::MALE => 60.0,
                GenderCode::FEMALE => 56.0,
            ],
            PropertyCode::HEIGHT_IN_CM => 160.0,
            PropertyCode::HEIGHT => 4,
            PropertyCode::AGE => 10,
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