<?php declare(strict_types=1);

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
     */
    public function I_am_stopped_by_specific_exception_on_invalid_value()
    {
        $this->expectException(\Granam\IntegerEnum\Exceptions\WrongValueForIntegerEnum::class);
        $this->expectExceptionMessageRegExp('~Broken heart by fixed dart~');
        AfflictionSize::getIt('Broken heart by fixed dart');
    }

    /**
     * @test
     */
    public function I_can_not_use_negative_value()
    {
        $this->expectException(\DrdPlus\Health\Afflictions\Exceptions\AfflictionSizeCanNotBeNegative::class);
        $this->expectExceptionMessageRegExp('~-1~');
        AfflictionSize::getEnum(-1);
    }
}