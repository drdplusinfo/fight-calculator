<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\Tests\ExceptionsHierarchy\Exceptions\ExceptionsUsedInParentDirs\PerfectLife;
use Granam\Tests\ExceptionsHierarchy\Exceptions\ExceptionsUsedInParentDirs\PerfectSociety\PerfectFamily\PerfectMamaAtWork;

class UsedCustomExceptionsAreFoundByRootNamespaceTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        $reflection = new \ReflectionClass(PerfectMamaAtWork::getClass());

        return $reflection->getNamespaceName();
    }

    protected function getRootNamespace(): string
    {
        $reflection = new \ReflectionClass(PerfectLife::getClass());

        return $reflection->getNamespaceName();
    }
}
