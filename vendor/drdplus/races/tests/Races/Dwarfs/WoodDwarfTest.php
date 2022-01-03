<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races\Dwarfs;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;

class WoodDwarfTest extends DwarfTest
{
    protected function getExpectedBaseProperty($genderCode, $propertyCode)
    {
        $properties = [
            GenderCode::MALE => [
                PropertyCode::STRENGTH => 1,
                PropertyCode::AGILITY => -1,
                PropertyCode::KNACK => 0,
                PropertyCode::WILL => 1,
                PropertyCode::INTELLIGENCE => -1,
                PropertyCode::CHARISMA => -1,
            ],
            GenderCode::FEMALE => [
                PropertyCode::STRENGTH => 1,
                PropertyCode::AGILITY => -1,
                PropertyCode::KNACK => -1,
                PropertyCode::WILL => 1,
                PropertyCode::INTELLIGENCE => 0,
                PropertyCode::CHARISMA => -1,
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
            return 20;
        }

        return parent::getExpectedOtherProperty($propertyCode, $genderCode);
    }

}