<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Orcs;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tests\Races\RaceTest;

abstract class OrcTest extends RaceTest
{
    protected function getExpectedOtherProperty($propertyCode, $genderCode)
    {
        $properties = [
            PropertyCode::SENSES => 1,
            PropertyCode::TOUGHNESS => 0,
            PropertyCode::INFRAVISION => true,
            PropertyCode::NATIVE_REGENERATION => false,
            PropertyCode::REQUIRES_DM_AGREEMENT => true,
            PropertyCode::REMARKABLE_SENSE => PropertyCode::SMELL,
        ];

        return $properties[$propertyCode];
    }
}