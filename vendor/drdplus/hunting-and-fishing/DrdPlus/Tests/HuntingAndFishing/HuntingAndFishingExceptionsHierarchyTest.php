<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\HuntingAndFishing;

use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class HuntingAndFishingExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
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
        return \str_replace('\\Tests', '', __NAMESPACE__);
    }

}