<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Exceptions;

use DrdPlus\Skills\Skills;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class SkillsExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace()
    {
        return $this->getRootNamespace();
    }

    /**
     * @return string
     */
    protected function getRootNamespace()
    {
        $reflection = new \ReflectionClass(Skills::class);

        return $reflection->getNamespaceName();
    }

}