<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Races;

use DrdPlus\Races\Race;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class RacesExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace()
    {
        $reflection = new \ReflectionClass(Race::class);

        return $reflection->getNamespaceName();
    }

    /**
     * @return string
     */
    protected function getRootNamespace()
    {
        return $this->getTestedNamespace();
    }

}