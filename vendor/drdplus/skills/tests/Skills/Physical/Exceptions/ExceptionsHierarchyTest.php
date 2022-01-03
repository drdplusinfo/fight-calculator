<?php declare(strict_types=1);

namespace DrdPlus\Tests\Skills\Physical\Exceptions;

use DrdPlus\Skills\Skills;
use DrdPlus\Skills\Physical\PhysicalSkills;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class ExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return (new \ReflectionClass(PhysicalSkills::class))->getNamespaceName();
    }

    protected function getRootNamespace(): string
    {
        return (new \ReflectionClass(Skills::class))->getNamespaceName();
    }

}
