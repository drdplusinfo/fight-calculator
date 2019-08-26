<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined\Exceptions;

use DrdPlus\Skills\Combined\CombinedSkills;
use DrdPlus\Skills\Skills;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class CombinedSkillsExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace()
    {
        $combinedSkills = new \ReflectionClass(CombinedSkills::class);

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