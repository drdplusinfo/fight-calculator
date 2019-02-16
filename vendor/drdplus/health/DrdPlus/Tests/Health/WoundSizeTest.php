<?php
namespace DrdPlus\Tests\Health;

use DrdPlus\Health\WoundSize;
use Granam\Integer\IntegerInterface;
use PHPUnit\Framework\TestCase;

class WoundSizeTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_use_it_as_an_integer(): void
    {
        $woundSize = new WoundSize(123);
        self::assertInstanceOf(IntegerInterface::class, $woundSize);
        self::assertSame(123, $woundSize->getValue());
        $woundSizeByFactory = WoundSize::createIt(123);
        self::assertEquals($woundSize, $woundSizeByFactory);
        self::assertNotSame($woundSize, $woundSizeByFactory);
    }

    /**
     * @test
     * @expectedException \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @expectedExceptionMessageRegExp ~Terribly wounded by horrible pebble~
     */
    public function I_am_stopped_by_specific_exception_on_invalid_value(): void
    {
        new WoundSize('Terribly wounded by horrible pebble');
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\WoundSizeCanNotBeNegative
     * @expectedExceptionMessageRegExp ~-1~
     */
    public function I_can_not_use_negative_value(): void
    {
        new WoundSize(-1);
    }
}