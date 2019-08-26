<?php declare(strict_types=1);

namespace Granam\Tests\FloatEnum;

use Granam\FloatEnum\FloatEnum;
use Granam\Float\FloatInterface;
use Granam\ScalarEnum\ScalarEnumInterface;
use Granam\Tests\ScalarEnum\Helpers\WithToStringTestObject;
use PHPUnit\Framework\TestCase;

class FloatEnumTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $enumClass = $this->getEnumClass();
        $floatEnum = $enumClass::getEnum($value = 12345.0);

        self::assertInstanceOf($enumClass, $floatEnum);
        self::assertInstanceOf(FloatInterface::class, $floatEnum);
        self::assertSame($value, $floatEnum->getValue());
    }

    /**
     * @return \Granam\FloatEnum\FloatEnum|string
     */
    protected function getEnumClass()
    {
        return FloatEnum::class;
    }

    /**
     * @test
     */
    public function I_will_get_the_same_value_as_created_with()
    {
        $enumClass = $this->getEnumClass();
        $floatEnum = $enumClass::getEnum($value = 12345.6789);

        self::assertSame($value, $floatEnum->getValue());
        self::assertSame((string)$value, (string)$floatEnum);
    }

    /**
     * @test
     */
    public function I_get_float_from_string_number()
    {
        $enumClass = $this->getEnumClass();
        $floatEnum = $enumClass::getEnum($stringFloat = '12345.6789');

        self::assertSame((float)$stringFloat, $floatEnum->getValue());
        self::assertSame($stringFloat, (string)$floatEnum);
    }

    /**
     * @test
     */
    public function I_get_float_from_value_wrapped_by_white_characters()
    {
        $enumClass = $this->getEnumClass();
        $floatEnum = $enumClass::getEnum(" \n\t\r\r\n 12.34 \t\t\r  ");

        self::assertSame(12.34, $floatEnum->getValue());
        self::assertSame('12.34', (string)$floatEnum);
    }

    /**
     * @test
     */
    public function I_get_float_from_integer()
    {
        $enumClass = $this->getEnumClass();
        $floatEnum = $enumClass::getEnum(123);

        self::assertSame(123.0, $floatEnum->getValue());
    }

    /**
     * @test
     */
    public function I_get_float_from_string_integer()
    {
        $enumClass = $this->getEnumClass();
        $floatEnum = $enumClass::getEnum('123');

        self::assertSame(123.0, $floatEnum->getValue());
    }

    /**
     * @test
     */
    public function I_can_not_use_value_with_trailing_trash()
    {
        $this->expectException(\Granam\FloatEnum\Exceptions\WrongValueForFloatEnum::class);
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum('12.34foo');
    }

    /**
     * @test
     */
    public function I_get_float_from_to_string_convertible_object()
    {
        $enumClass = $this->getEnumClass();

        $floatEnum = $enumClass::getEnum(new WithToStringTestObject($floatValue = 12345.6789));
        self::assertInstanceOf(ScalarEnumInterface::class, $floatEnum);
        self::assertSame($floatValue, $floatEnum->getValue());
        self::assertSame((string)$floatValue, (string)$floatEnum);

        $floatEnum = $enumClass::getEnum(new WithToStringTestObject($integerValue = 12345));
        self::assertInstanceOf(ScalarEnumInterface::class, $floatEnum);
        self::assertSame((float)$integerValue, $floatEnum->getValue());
        self::assertSame((string)$integerValue, (string)$floatEnum);
    }

    /**
     * @test
     * @dataProvider provideNonNumericValue
     * @param mixed $nonNumericValue
     */
    public function I_can_not_create_enum_from_non_numeric_value($nonNumericValue)
    {
        $this->expectException(\Granam\FloatEnum\Exceptions\WrongValueForFloatEnum::class);
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum($nonNumericValue);
    }

    public function provideNonNumericValue(): array
    {
        return [
            ['foo'],
            [new WithToStringTestObject('foo')],
            [''],
            [null],
        ];
    }

    /**
     * @test
     */
    public function inherited_enum_with_same_value_lives_in_own_inner_namespace()
    {
        $enumClass = $this->getEnumClass();

        $floatEnum = $enumClass::getEnum($value = 12345.6789);
        self::assertInstanceOf($enumClass, $floatEnum);
        self::assertSame($value, $floatEnum->getValue());
        self::assertSame((string)$value, (string)$floatEnum);

        $inDifferentNamespace = $this->getInheritedEnum($value);
        self::assertInstanceOf($enumClass, $inDifferentNamespace);
        self::assertSame($floatEnum->getValue(), $inDifferentNamespace->getValue());
        self::assertNotSame($floatEnum, $inDifferentNamespace);
    }

    protected function getInheritedEnum($value)
    {
        return TestInheritedFloatEnum::getEnum($value);
    }
}

/** inner */
class TestInheritedFloatEnum extends FloatEnum
{
}