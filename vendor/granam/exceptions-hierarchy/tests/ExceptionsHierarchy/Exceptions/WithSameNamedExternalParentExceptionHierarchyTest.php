<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class WithSameNamedExternalParentExceptionHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy\WithSameNamedExternalParent';
    }

    protected function getExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the tested namespace
    }

    protected function getExternalRootNamespaces(): array
    {
        return [
            __NAMESPACE__ . '\DummyExceptionsHierarchy\WithSameNamedParent\Children\IAmHereForWithSameNamedExternalParent',
        ];
    }

    protected function getExternalRootExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the external root namespace
    }

    protected function getExceptionClassesSkippedFromUsageTest(): array
    {
        return [
            'Granam\Tests\Exceptions\Tools\DummyExceptionsHierarchy\WithSameNamedExternalParent\IAmSameNamed',
        ];
    }

}
