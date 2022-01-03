<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy;

class WithSameNamedExternalNonParentExceptionHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace(): string
    {
        return __NAMESPACE__ . '\\DummyExceptionsHierarchy\\WithSameNamedExternalNonParent';
    }

    protected function getExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the tested namespace
    }

    protected function getExternalRootNamespaces(): array
    {
        return [
            __NAMESPACE__ . '\\DummyExceptionsHierarchy\\WithSameNamedParent',
        ];
    }

    protected function getExternalRootExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the external root namespace
    }

    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(InvalidExceptionHierarchy::class);
        $this->expectExceptionMessageMatches('~Exception .+\\\WithSameNamedExternalNonParent\\\IAmSameNamed .+ parent .+\\\WithSameNamedParent\\\IAmSameNamed~');
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getExceptionClassesSkippedFromUsageTest(): array
    {
        return [
            DummyExceptionsHierarchy\WithSameNamedExternalNonParent\IAmSameNamed::class,
        ];
    }

}
