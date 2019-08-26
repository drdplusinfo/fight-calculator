<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Hobbits;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tests\Races\RaceTest;

class CommonHobbitTest extends RaceTest
{
    protected function getExpectedBaseProperty($genderCode, $propertyCode)
    {
        $properties = [
            PropertyCode::STRENGTH => [
                GenderCode::MALE => -3,
                GenderCode::FEMALE => -4,
            ],
            PropertyCode::AGILITY => [
                GenderCode::MALE => 1,
                GenderCode::FEMALE => 2,
            ],
            PropertyCode::KNACK => [
                GenderCode::MALE => 1,
                GenderCode::FEMALE => 0,
            ],
            PropertyCode::WILL => 0,
            PropertyCode::INTELLIGENCE => -1,
            PropertyCode::CHARISMA => [
                GenderCode::MALE => 2,
                GenderCode::FEMALE => 3,
            ],
        ];

        return isset($properties[$propertyCode][$genderCode])
            ? $properties[$propertyCode][$genderCode]
            : $properties[$propertyCode];
    }

    protected function getExpectedOtherProperty($propertyCode, $genderCode)
    {
        $properties = [
            PropertyCode::SENSES => 0,
            PropertyCode::TOUGHNESS => 0,
            PropertyCode::SIZE => [
                GenderCode::MALE => -2,
                GenderCode::FEMALE => -3,
            ],
            PropertyCode::BODY_WEIGHT_IN_KG => [
                GenderCode::MALE => 40.0,
                GenderCode::FEMALE => 36.0
            ],
            PropertyCode::HEIGHT_IN_CM => 110.0,
            PropertyCode::HEIGHT => 1,
            PropertyCode::INFRAVISION => false,
            PropertyCode::NATIVE_REGENERATION => false,
            PropertyCode::REQUIRES_DM_AGREEMENT => false,
            PropertyCode::REMARKABLE_SENSE => PropertyCode::TASTE,
            PropertyCode::AGE => 25,
        ];

        return isset($properties[$propertyCode][$genderCode])
            ? $properties[$propertyCode][$genderCode]
            : $properties[$propertyCode];
    }
}