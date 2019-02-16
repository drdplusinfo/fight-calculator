<?php
namespace DrdPlus\Tests\Health;

use DrdPlus\Health\Afflictions\AfflictionSize;
use Granam\Integer\IntegerInterface;
use PHPUnit\Framework\TestCase;

class AfflictionSizeTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_use_it_as_an_integer()
    {
        $afflictionSize = AfflictionSize::getIt(123);
        self::assertInstanceOf(IntegerInterface::class, $afflictionSize);
        self::assertSame(123, $afflictionSize->getValue());
    }

    /**
     * @test
     * @expectedException \Granam\IntegerEnum\Exceptions\WrongValueForIntegerEnum
     * @expectedExceptionMessageRegExp ~Broken heart by fixed dart~
     */
    public function I_am_stopped_by_specific_exception_on_invalid_value()
    {
        AfflictionSize::getIt('Broken heart by fixed dart');
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Afflictions\Exceptions\AfflictionSizeCanNotBeNegative
     * @expectedExceptionMessageRegExp ~-1~
     */
    public function I_can_not_use_negative_value()
    {
        AfflictionSize::getEnum(-1);
    }
}