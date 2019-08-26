<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical\Exceptions;

use DrdPlus\Skills\Skills;
use DrdPlus\Skills\Physical\PhysicalSkills;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class ExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace()
    {
        $combinedSkills = new \ReflectionClass(PhysicalSkills::class);

        return $combinedSkills->getNamespaceName();
    }

    /**
     * @return string
     */
    protected function getRootNamespace()
    {
        $skills = new \ReflectionClass(Skills::class);

        return $skills->getNamespaceName();
    }

}