<?php
declare(strict_types=1);

namespace DrdPlus\Tests\PropertiesByFate;

use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class PropertiesByFateExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return \str_replace('\\Tests', '', __NAMESPACE__);
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

}