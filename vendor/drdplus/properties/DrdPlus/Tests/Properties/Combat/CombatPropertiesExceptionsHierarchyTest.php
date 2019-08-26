<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat;

use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class CombatPropertiesExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return \str_replace('\Tests', '', __NAMESPACE__);
    }

    protected function getRootNamespace(): string
    {
        return \str_replace(['\Tests', 'Combat'], '', __NAMESPACE__);
    }

}