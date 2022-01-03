<?php declare(strict_types=1);

namespace Granam\Tests\StringEnum;

use Granam\ScalarEnum\ScalarEnum;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class StringEnumExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace(): string
    {
        return \str_replace('\Tests', '', __NAMESPACE__);
    }

    protected function getExternalRootNamespaces(): array
    {
        $reflection = new \ReflectionClass(ScalarEnum::class);
        return [$reflection->getNamespaceName()];
    }

}
