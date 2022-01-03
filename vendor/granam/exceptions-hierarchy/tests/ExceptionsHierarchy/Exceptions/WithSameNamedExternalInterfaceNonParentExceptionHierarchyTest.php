<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class WithSameNamedExternalInterfaceNonParentExceptionHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace(): string
    {
        return __NAMESPACE__ . '\\DummyExceptionsHierarchy\\WithSameNamedExternalInterfaceNonParent';
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
        $this->expectException(\Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy::class);
        $this->expectExceptionMessageMatches('~Tag interface .+\\\WithSameNamedExternalInterfaceNonParent\\\Exception .+external parent tag interface .+\\\WithSameNamedParent\\\Exception~');
        parent::My_exceptions_are_in_family_tree();
    }
}
