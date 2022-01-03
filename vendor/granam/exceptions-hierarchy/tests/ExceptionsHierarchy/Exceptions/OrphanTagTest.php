<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class OrphanTagTest extends AbstractExceptionsHierarchyTest
{

    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(\Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound::class);
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy\OrphanTag';
    }

    protected function getExceptionsSubDir(): string
    {
        return '';
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

    public function My_exceptions_are_used()
    {
        // disabling original test
        return false;
    }

}
