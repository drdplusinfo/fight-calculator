<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy;

class WithSameNamedNonParentExceptionHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace() . '\\WithSameNamedNonParent\\Children';
    }

    protected function getRootNamespace(): string
    {
        return __NAMESPACE__ . '\\DummyExceptionsHierarchy';
    }

    protected function getExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the tested namespace
    }

    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(InvalidExceptionHierarchy::class);
        $this->expectExceptionMessageMatches('~^Exception .+\\\WithSameNamedNonParent\\\Children\\\IAmSameNamedButNotFromFamily should extends parent .+\\\WithSameNamedNonParent\\\IAmSameNamedButNotFromFamily~');
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getExceptionClassesSkippedFromUsageTest(): array
    {
        return [
            DummyExceptionsHierarchy\WithSameNamedNonParent\Children\IAmSameNamedButNotFromFamily::class,
            DummyExceptionsHierarchy\WithSameNamedNonParent\IAmSameNamedButNotFromFamily::class,
            DummyExceptionsHierarchy\IAmLogicException::class,
            DummyExceptionsHierarchy\IAmRuntimeException::class,
        ];
    }

}
