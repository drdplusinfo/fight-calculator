<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\Tests\ExceptionsHierarchy\Exceptions\DummyExceptionsHierarchy\IAmLogicException;
use Granam\Tests\ExceptionsHierarchy\Exceptions\DummyExceptionsHierarchy\IAmRuntimeException;

class InheritedTagsTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace() . '\InheritedTags';
    }

    protected function getExceptionsSubDir(): string
    {
        return '';
    }

    protected function getRootNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy';
    }

    protected function getExceptionClassesSkippedFromUsageTest(): array
    {
        return [
            IAmLogicException::class,
            IAmRuntimeException::class,
        ];
    }

}
