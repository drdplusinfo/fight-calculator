<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions;

use DrdPlus\Health\Afflictions\AfflictionProperty;
use PHPUnit\Framework\TestCase;

class AfflictionPropertyTest extends TestCase
{
    /**
     * @test
     * @dataProvider providePropertyCode
     * @param string $propertyCode
     */
    public function I_can_use_it($propertyCode)
    {
        $afflictionProperty = AfflictionProperty::getIt($propertyCode);
        self::assertInstanceOf(AfflictionProperty::class, $afflictionProperty);
        self::assertSame($propertyCode, $afflictionProperty->getValue());
    }

    public function providePropertyCode()
    {
        return array_map(
            function ($propertyCode) {
                return [$propertyCode];
            },
            $this->getPropertyCodes()
        );
    }

    private function getPropertyCodes()
    {
        return [
            'strength',
            'agility',
            'knack',
            'will',
            'intelligence',
            'charisma',
            'endurance',
            'toughness',
            'level',
        ];
    }

    /**
     * @test
     */
    public function I_can_not_use_custom_property()
    {
        $this->expectException(\DrdPlus\Health\Afflictions\Exceptions\UnknownAfflictionPropertyCode::class);
        $this->expectExceptionMessageMatches('~greedy~');
        AfflictionProperty::getIt('greedy');
    }
}
