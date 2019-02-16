<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Background\BackgroundParts;

use DrdPlus\Background\Background;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class BackgroundPartsExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace(): string
    {
        return \str_replace('\Tests', '', __NAMESPACE__);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function getRootNamespace(): string
    {
        $rootReflection = new \ReflectionClass(Background::class);

        return $rootReflection->getNamespaceName();
    }

}