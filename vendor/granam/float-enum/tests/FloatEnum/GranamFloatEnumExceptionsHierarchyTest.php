<?php declare(strict_types=1);

namespace Granam\Tests\FloatEnum;

use Granam\ScalarEnum\ScalarEnum;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class GranamFloatEnumExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
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
        $externalRootReflection = new \ReflectionClass(ScalarEnum::class);

        return [$externalRootReflection->getNamespaceName()];
    }

}
