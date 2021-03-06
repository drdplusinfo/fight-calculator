<?php declare(strict_types=1);

namespace Granam\Tests\StringEnum;

use Granam\StringEnum\StringEnum;
use Granam\String\StringInterface;
use PHPUnit\Framework\TestCase;

class StringEnumTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $stringEnum = StringEnum::getEnum($value = 'foo');
        self::assertInstanceOf(StringEnum::class, $stringEnum);
        self::assertSame($value, $stringEnum->getValue());
        self::assertInstanceOf(StringInterface::class, $stringEnum);
    }

    /**
     * @test
     */
    public function I_can_not_use_null()
    {
        $this->expectException(\Granam\StringEnum\Exceptions\WrongValueForStringEnum::class);
        $this->expectExceptionMessageRegExp('~got NULL$~');
        StringEnum::getEnum(null);
    }
}