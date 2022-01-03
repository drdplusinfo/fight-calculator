<?php declare(strict_types=1);

namespace DrdPlus\Tests\Races;

use DrdPlus\Races\Race;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class RacesExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        $reflection = new \ReflectionClass(Race::class);

        return $reflection->getNamespaceName();
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

}
