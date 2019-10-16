<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\Tests\AttackSkeleton\Partials\AttackCalculatorTestTrait;
use DrdPlus\Tests\CalculatorSkeleton\CalculatorSkeletonTestsExceptionsHierarchyTest;

class AttackSkeletonTestsExceptionsHierarchyTest extends CalculatorSkeletonTestsExceptionsHierarchyTest
{
    use AttackCalculatorTestTrait;

    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace(): string
    {
        return __NAMESPACE__;
    }

}