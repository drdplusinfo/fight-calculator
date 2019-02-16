<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Tests\BaseProperties\Partials\PropertyTest;

abstract class AbstractDerivedPropertyTest extends PropertyTest
{
    /**
     * @test
     */
    public function I_can_get_property_easily()
    {
        /** @var AbstractDerivedProperty|string $sutClass */
        $sutClass = self::getSutClass();
        $property = $this->createIt(223344);
        self::assertInstanceOf($sutClass, $property);
        /** @var AbstractDerivedProperty $property */
        self::assertSame('223344', (string)$property->getValue());
        self::assertSame('223344', (string)$property);
        self::assertSame(
            PropertyCode::getIt($this->getExpectedPropertyCode()),
            $property->getCode(),
            'We expected ' . PropertyCode::class . " with value '{$this->getExpectedPropertyCode()}'"
            . ', got ' . \get_class($property->getCode()) . " with value '{$property->getCode()->getValue()}'"
        );
    }

    /**
     * @test
     */
    public function I_can_add_value()
    {
        $property = $this->createIt(123);

        $greater = $property->add(456);
        self::assertSame(123, $property->getValue());
        self::assertNotEquals($property, $greater);
        self::assertSame(579, $greater->getValue());

        $double = $greater->add($greater);
        self::assertSame(1158, $double->getValue());
    }

    abstract protected function createIt(int $value): AbstractDerivedProperty;

    /**
     * @test
     */
    public function I_can_subtract_value()
    {
        $property = $this->createIt(123);

        $lesser = $property->sub(456);
        self::assertSame(123, $property->getValue());
        self::assertNotEquals($property, $lesser);
        self::assertSame(-333, $lesser->getValue());

        $zero = $lesser->sub($lesser);
        self::assertSame(0, $zero->getValue());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function Has_modifying_methods_annotated_return_value()
    {
        $reflectionClass = new \ReflectionClass(self::getSutClass());
        $classBasename = \str_replace($reflectionClass->getNamespaceName() . '\\', '', $reflectionClass->getName());
        self::assertContains(<<<ANNOTATION
 * @method {$classBasename} add(int | \\Granam\\Integer\\IntegerInterface \$value)
 * @method {$classBasename} sub(int | \\Granam\\Integer\\IntegerInterface \$value)
ANNOTATION
            , $reflectionClass->getDocComment() ?: ''
        );
    }
}