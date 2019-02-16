<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Krolls;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tests\Races\RaceTest;

abstract class KrollTest extends RaceTest
{
    protected function getExpectedOtherProperty($propertyCode, $genderCode)
    {
        $properties = [
            PropertyCode::SENSES => 0,
            PropertyCode::TOUGHNESS => 0,
            PropertyCode::SIZE => [
                GenderCode::MALE => 3,
                GenderCode::FEMALE => 2,
            ],
            PropertyCode::BODY_WEIGHT_IN_KG => [
                GenderCode::MALE => 120.0,
                GenderCode::FEMALE => 110.0,
            ],
            PropertyCode::HEIGHT_IN_CM => 220.0,
            PropertyCode::HEIGHT => 7,
            PropertyCode::INFRAVISION => false,
            PropertyCode::NATIVE_REGENERATION => true,
            PropertyCode::REQUIRES_DM_AGREEMENT => false,
            PropertyCode::REMARKABLE_SENSE => PropertyCode::HEARING,
        ];

        return isset($properties[$propertyCode][$genderCode])
            ? $properties[$propertyCode][$genderCode]
            : $properties[$propertyCode];
    }
}