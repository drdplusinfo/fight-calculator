<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class TestOfExceptionHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return 'Granam\ExceptionsHierarchy';
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

}
