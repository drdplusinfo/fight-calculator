<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Partials;

use DrdPlus\Tests\BaseProperties\Partials\AbstractSimplePropertyTest;

abstract class AbstractFloatPropertyTest extends AbstractSimplePropertyTest
{
    protected function getValuesForTest(): array
    {
        return [0.01, 123.456];
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function Has_modifying_methods_return_value_annotated()
    {
        $reflectionClass = new \ReflectionClass(self::getSutClass());
        $classBasename = str_replace($reflectionClass->getNamespaceName() . '\\', '', $reflectionClass->getName());
        self::assertContains(<<<ANNOTATION
 * @method static {$classBasename} getIt(float | NumberInterface \$value)
ANNOTATION
            , $reflectionClass->getDocComment());
    }
}