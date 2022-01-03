<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class InvalidExceptionsUsageRootDirCauseExceptionTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\\DummyExceptionsHierarchy\\UnusedCustomException';
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

    protected function getExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the tested namespace
    }

    protected function getExceptionsUsageRootDir(): string
    {
        return __DIR__ . '/AnybodyAtHome';
    }

    public function My_exceptions_are_in_family_tree()
    {
        // disabled
        return false;
    }

    /**
     * @test
     */
    public function My_exceptions_are_used()
    {
        $this->expectException(\Granam\ExceptionsHierarchy\Exceptions\FolderCanNotBeRead::class);
        parent::My_exceptions_are_used();
    }

}
