<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Native;

use DrdPlus\Tests\Properties\Partials\AbstractBooleanPropertyTest;

abstract class NativePropertyTest extends AbstractBooleanPropertyTest
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function Its_factory_method_has_return_value_annotated()
    {
        $reflectionClass = new \ReflectionClass(self::getSutClass());
        $classBasename = preg_replace('~^.+[\\\](\w+)$~', '$1', self::getSutClass());
        self::assertStringContainsString(<<<ANNOTATION
/**
 * @method static {$classBasename} getIt(\$value)
 */
ANNOTATION
            , (string)$reflectionClass->getDocComment());
    }
}
