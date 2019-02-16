<?php
declare(strict_types=1);

namespace Granam\Tests\FloatEnum;

use Granam\ScalarEnum\ScalarEnum;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class GranamFloatEnumExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    /**
     * @return string
     */
    protected function getRootNamespace(): string
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function getExternalRootNamespaces(): string
    {
        $externalRootReflection = new \ReflectionClass(ScalarEnum::class);

        return $externalRootReflection->getNamespaceName();
    }

}