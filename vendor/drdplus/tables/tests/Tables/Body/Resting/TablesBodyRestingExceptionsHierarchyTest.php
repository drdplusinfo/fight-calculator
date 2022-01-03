<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Tables\Body\Resting;

use DrdPlus\Tables\Table;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class TablesBodyRestingExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace(): string
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    /**
     * @return string
     */
    protected function getRootNamespace(): string
    {
        $tableReflection = new \ReflectionClass(Table::class);

        return $tableReflection->getNamespaceName();
    }

}