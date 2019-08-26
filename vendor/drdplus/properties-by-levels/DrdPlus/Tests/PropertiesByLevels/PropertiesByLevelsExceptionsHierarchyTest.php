<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\PropertiesByLevels;

use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class PropertiesByLevelsExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
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
        return str_replace('\Tests', '', __NAMESPACE__);
    }

}