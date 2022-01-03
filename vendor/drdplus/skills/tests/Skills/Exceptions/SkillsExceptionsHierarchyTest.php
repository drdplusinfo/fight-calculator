<?php declare(strict_types=1);

namespace DrdPlus\Tests\Skills\Exceptions;

use DrdPlus\Skills\Skills;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class SkillsExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace(): string
    {
        return (new \ReflectionClass(Skills::class))->getNamespaceName();
    }

}
