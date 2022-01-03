<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Krolls;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;

class WildKrollTest extends KrollTest
{
    protected function getExpectedBaseProperty($genderCode, $propertyCode)
    {
        $properties = [
            GenderCode::MALE => [
                PropertyCode::STRENGTH => 3,
                PropertyCode::AGILITY => -1,
                PropertyCode::KNACK => -2,
                PropertyCode::WILL => 2,
                PropertyCode::INTELLIGENCE => -3,
                PropertyCode::CHARISMA => -2,
            ],
            GenderCode::FEMALE => [
                PropertyCode::STRENGTH => 2,
                PropertyCode::AGILITY => 0,
                PropertyCode::KNACK => -2,
                PropertyCode::WILL => 1,
                PropertyCode::INTELLIGENCE => -3,
                PropertyCode::CHARISMA => -1,
            ],
        ];

        return $properties[$genderCode][$propertyCode];
    }

    protected function getExpectedOtherProperty($propertyCode, $genderCode)
    {
        if ($propertyCode === PropertyCode::REQUIRES_DM_AGREEMENT) {
            return true;
        }
        if ($propertyCode === PropertyCode::AGE) {
            return 11;
        }

        return parent::getExpectedOtherProperty($propertyCode, $genderCode);
    }

}