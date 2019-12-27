<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\Tests\AttackSkeleton\Partials\AttackCalculatorTestTrait;
use DrdPlus\Tests\CalculatorSkeleton\CalculatorSkeletonExceptionsHierarchyTest;

class AttackSkeletonExceptionsHierarchyTest extends CalculatorSkeletonExceptionsHierarchyTest
{
    use AttackCalculatorTestTrait;

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
        return $this->getTestedNamespace();
    }

}