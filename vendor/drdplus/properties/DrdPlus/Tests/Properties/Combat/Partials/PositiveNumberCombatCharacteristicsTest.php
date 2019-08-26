<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat\Partials;

abstract class PositiveNumberCombatCharacteristicsTest extends CombatCharacteristicTest
{
    /**
     * @test
     */
    public function I_can_add_value_and_subtract_from_it()
    {
        $sut = $this->createSut();
        $increased = $sut->add(123);
        self::assertNotEquals($sut, $increased);
        self::assertSame($sut->getValue() + 123, $increased->getValue());
        $double = $increased->add($increased);
        self::assertSame($increased->getValue() * 2, $double->getValue());

        $decreased = $sut->sub(111);
        self::assertNotEquals($sut, $decreased);
        self::assertSame($sut->getValue() - 111, $decreased->getValue());
        $zeroed = $decreased->sub($decreased);
        self::assertSame(0, $zeroed->getValue());
    }
}