<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Humans;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;

class CommonHumanTest extends HumanTest
{
    protected function getExpectedBaseProperty($genderCode, $propertyCode)
    {
        $properties = [
            GenderCode::MALE => [
                PropertyCode::STRENGTH => 0,
                PropertyCode::AGILITY => 0,
                PropertyCode::KNACK => 0,
                PropertyCode::WILL => 0,
                PropertyCode::INTELLIGENCE => 0,
                PropertyCode::CHARISMA => 0,
            ],
            GenderCode::FEMALE => [
                PropertyCode::STRENGTH => -1,
                PropertyCode::AGILITY => 0,
                PropertyCode::KNACK => 0,
                PropertyCode::WILL => 0,
                PropertyCode::INTELLIGENCE => 0,
                PropertyCode::CHARISMA => 1,
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
            return 15;
        }

        return parent::getExpectedOtherProperty($propertyCode, $genderCode);
    }
}