<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Krolls;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;

class CommonKrollTest extends KrollTest
{
    protected function getExpectedBaseProperty($genderCode, $propertyCode)
    {
        $properties = [
            GenderCode::MALE => [
                PropertyCode::STRENGTH => 3,
                PropertyCode::AGILITY => -2,
                PropertyCode::KNACK => -1,
                PropertyCode::WILL => 1,
                PropertyCode::INTELLIGENCE => -3,
                PropertyCode::CHARISMA => -1,
            ],
            GenderCode::FEMALE => [
                PropertyCode::STRENGTH => 2,
                PropertyCode::AGILITY => -1,
                PropertyCode::KNACK => -1,
                PropertyCode::WILL => 0,
                PropertyCode::INTELLIGENCE => -3,
                PropertyCode::CHARISMA => 0,
            ],
        ];

        return $properties[$genderCode][$propertyCode];
    }

    /**
     * @param string $propertyCode
     * @param string $genderCode
     * @return string|int|float|bool
     */
    protected function getExpectedOtherProperty($propertyCode, $genderCode)
    {
        if ($propertyCode === PropertyCode::AGE) {
            return 12;
        }

        return parent::getExpectedOtherProperty($propertyCode, $genderCode);
    }
}