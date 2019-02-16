<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Dwarfs;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;

class MountainDwarfTest extends DwarfTest
{
    protected function getExpectedBaseProperty($genderCode, $propertyCode)
    {
        $properties = [
            GenderCode::MALE => [
                PropertyCode::STRENGTH => 2,
                PropertyCode::AGILITY => -1,
                PropertyCode::KNACK => 0,
                PropertyCode::WILL => 2,
                PropertyCode::INTELLIGENCE => -2,
                PropertyCode::CHARISMA => -2,
            ],
            GenderCode::FEMALE => [
                PropertyCode::STRENGTH => 2,
                PropertyCode::AGILITY => -1,
                PropertyCode::KNACK => -1,
                PropertyCode::WILL => 2,
                PropertyCode::INTELLIGENCE => -1,
                PropertyCode::CHARISMA => -2,
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
            return 18;
        }

        return parent::getExpectedOtherProperty($propertyCode, $genderCode);
    }
}