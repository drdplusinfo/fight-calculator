<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Elves;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tests\Races\RaceTest;

abstract class ElfTest extends RaceTest
{
    protected function getExpectedOtherProperty($propertyCode, $genderCode)
    {
        $properties = [
            PropertyCode::SENSES => 0,
            PropertyCode::TOUGHNESS => -1,
            PropertyCode::SIZE => [
                GenderCode::MALE => -1,
                GenderCode::FEMALE => -2,
            ],
            PropertyCode::BODY_WEIGHT_IN_KG => [
                GenderCode::MALE => 50.0,
                GenderCode::FEMALE => 45.0,
            ],
            PropertyCode::HEIGHT_IN_CM => 160.0,
            PropertyCode::HEIGHT => 4,
            PropertyCode::INFRAVISION => false,
            PropertyCode::NATIVE_REGENERATION => false,
            PropertyCode::REQUIRES_DM_AGREEMENT => false,
            PropertyCode::REMARKABLE_SENSE => PropertyCode::SIGHT,
        ];

        return isset($properties[$propertyCode][$genderCode])
            ? $properties[$propertyCode][$genderCode]
            : $properties[$propertyCode];
    }
}