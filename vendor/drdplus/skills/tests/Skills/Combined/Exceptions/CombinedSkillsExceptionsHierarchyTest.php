<?php declare(strict_types=1);

namespace DrdPlus\Tests\Skills\Combined\Exceptions;

use DrdPlus\Skills\Combined\CombinedSkills;
use DrdPlus\Skills\Skills;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class CombinedSkillsExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return (new \ReflectionClass(CombinedSkills::class))->getNamespaceName();
    }

    protected function getRootNamespace(): string
    {
        return (new \ReflectionClass(Skills::class))->getNamespaceName();
    }

}
