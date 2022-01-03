<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class LogicTagBrokenLineageTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(\Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy::class);
        $this->expectExceptionMessageMatches('~^Tag .+\\\BrokenLineage\\\LogicTagWithoutParent\\\Logic should be child of .+\\\BrokenLineage\\\Logic~');
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace() . '\\LogicTagWithoutParent';
    }

    protected function getRootNamespace(): string
    {
        return __NAMESPACE__ . '\\DummyExceptionsHierarchy\\BrokenLineage';
    }

    protected function getExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the tested namespace
    }

}
