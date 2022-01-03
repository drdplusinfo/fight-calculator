<?php declare(strict_types=1);

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
     */
    public function I_am_stopped_by_specific_exception_on_invalid_value(): void
    {
        $this->expectException(\Granam\Integer\Tools\Exceptions\WrongParameterType::class);
        $this->expectExceptionMessageMatches('~Terribly wounded by horrible pebble~');
        new WoundSize('Terribly wounded by horrible pebble');
    }

    /**
     * @test
     */
    public function I_can_not_use_negative_value(): void
    {
        $this->expectException(\DrdPlus\Health\Exceptions\WoundSizeCanNotBeNegative::class);
        $this->expectExceptionMessageMatches('~-1~');
        new WoundSize(-1);
    }
}
