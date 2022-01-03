<?php declare(strict_types=1);

namespace Tests\DrdPlus\AttackSkeleton;

use Tests\DrdPlus\AttackSkeleton\Partials\AttackCalculatorTestTrait;
use Tests\DrdPlus\CalculatorSkeleton\CalculatorSkeletonTestsExceptionsHierarchyTest;

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
