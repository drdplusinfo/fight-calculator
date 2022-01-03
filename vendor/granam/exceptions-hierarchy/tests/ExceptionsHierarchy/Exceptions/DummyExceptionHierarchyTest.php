<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\Tests\ExceptionsHierarchy\Exceptions\DummyExceptionsHierarchy\IAmLogicException;
use Granam\Tests\ExceptionsHierarchy\Exceptions\DummyExceptionsHierarchy\IAmRuntimeException;

class DummyExceptionHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\\DummyExceptionsHierarchy';
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

    protected function getExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the tested namespace
    }

    protected function getExceptionClassesSkippedFromUsageTest(): array
    {
        return [
            IAmLogicException::class,
            IAmRuntimeException::class,
        ];
    }
}
