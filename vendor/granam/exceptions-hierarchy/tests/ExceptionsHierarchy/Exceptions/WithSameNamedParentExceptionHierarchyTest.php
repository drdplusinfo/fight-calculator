<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class WithSameNamedParentExceptionHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace() . '\\WithSameNamedParent\\Children';
    }

    protected function getRootNamespace(): string
    {
        return __NAMESPACE__ . '\\DummyExceptionsHierarchy';
    }

    protected function getExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the tested namespace
    }

    protected function getExceptionClassesSkippedFromUsageTest(): array
    {
        return [
            DummyExceptionsHierarchy\WithSameNamedParent\Children\IAmSameNamed::class,
            DummyExceptionsHierarchy\WithSameNamedParent\IAmSameNamed::class,
            DummyExceptionsHierarchy\IAmLogicException::class,
            DummyExceptionsHierarchy\IAmRuntimeException::class,
        ];
    }

}
