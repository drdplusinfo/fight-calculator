<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Humans;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tests\Races\RaceTest;

abstract class HumanTest extends RaceTest
{
    protected function getExpectedOtherProperty($propertyCode, $genderCode)
    {
        $properties = [
            PropertyCode::SENSES => 0,
            PropertyCode::TOUGHNESS => 0,
            PropertyCode::SIZE => [
                GenderCode::MALE => 0,
                GenderCode::FEMALE => -1,
            ],
            PropertyCode::BODY_WEIGHT_IN_KG => [
                GenderCode::MALE => 80.0,
                GenderCode::FEMALE => 70.0,
            ],
            PropertyCode::HEIGHT_IN_CM => 180.0,
            PropertyCode::HEIGHT => 5,
            PropertyCode::INFRAVISION => false,
            PropertyCode::NATIVE_REGENERATION => false,
            PropertyCode::REQUIRES_DM_AGREEMENT => false,
            PropertyCode::REMARKABLE_SENSE => '',
        ];


        return isset($properties[$propertyCode][$genderCode])
            ? $properties[$propertyCode][$genderCode]
            : $properties[$propertyCode];
    }
}