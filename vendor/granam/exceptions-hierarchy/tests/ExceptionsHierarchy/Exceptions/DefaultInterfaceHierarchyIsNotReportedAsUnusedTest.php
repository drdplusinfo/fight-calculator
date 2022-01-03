<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class DefaultInterfaceHierarchyIsNotReportedAsUnusedTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\\DummyExceptionsHierarchy\\DefaultInterfaceHierarchy';
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

    protected function getExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the tested namespace
    }

}
