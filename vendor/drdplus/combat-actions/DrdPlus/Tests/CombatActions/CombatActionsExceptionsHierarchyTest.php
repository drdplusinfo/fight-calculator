<?php
declare(strict_types=1);

namespace DrdPlus\Tests\CombatActions;

use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class CombatActionsExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    /**
     * @return string
     */
    protected function getRootNamespace(): string
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

}