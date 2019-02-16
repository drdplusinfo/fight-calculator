<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Partials;

use DrdPlus\Properties\Partials\AbstractIntegerProperty;
use DrdPlus\Tests\BaseProperties\Partials\AbstractSimplePropertyTest;

abstract class AbstractIntegerPropertyTest extends AbstractSimplePropertyTest
{
    /**
     * @return int[]
     */
    protected function getValuesForTest(): array
    {
        return [0, 123456];
    }

    /**
     * @test
     */
    public function I_can_add_value()
    {
        /** @var AbstractIntegerProperty $propertyClass */
        $propertyClass = self::getSutClass();
        /** @var AbstractIntegerProperty $property */
        $property = $propertyClass::getIt(123);

        $greater = $property->add(456);
        self::assertSame(123, $property->getValue());
        self::assertNotEquals($property, $greater);
        self::assertSame(579, $greater->getValue());

        $double = $greater->add($greater);
        self::assertSame(1158, $double->getValue());
    }

    /**
     * @test
     */
    public function I_can_subtract_value()
    {
        /** @var AbstractIntegerProperty $propertyClass */
        $propertyClass = self::getSutClass();
        /** @var AbstractIntegerProperty $property */
        $property = $propertyClass::getIt(123);

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
        $classBasename = str_replace($reflectionClass->getNamespaceName() . '\\', '', $reflectionClass->getName());
        self::assertContains(<<<ANNOTATION
 * @method static {$classBasename} getIt(int | \\Granam\\Integer\\IntegerInterface \$value)
 * @method {$classBasename} add(int | \\Granam\\Integer\\IntegerInterface \$value)
 * @method {$classBasename} sub(int | \\Granam\\Integer\\IntegerInterface \$value)
ANNOTATION
            , $reflectionClass->getDocComment());
    }
}