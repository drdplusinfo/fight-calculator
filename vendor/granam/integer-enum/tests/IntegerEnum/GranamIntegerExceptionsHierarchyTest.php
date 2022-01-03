<?php declare(strict_types=1);

namespace Granam\Tests\IntegerEnum;

use Granam\ScalarEnum\ScalarEnumInterface;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class GranamIntegerExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace(): string
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    protected function getExternalRootNamespaces(): array
    {
        $externalRootReflection = new \ReflectionClass(ScalarEnumInterface::class);

        return [$externalRootReflection->getNamespaceName()];
    }

}
