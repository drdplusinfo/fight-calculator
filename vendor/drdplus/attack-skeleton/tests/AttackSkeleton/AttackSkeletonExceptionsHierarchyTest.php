<?php declare(strict_types=1);

namespace Tests\DrdPlus\AttackSkeleton;

use Tests\DrdPlus\AttackSkeleton\Partials\AttackCalculatorTestTrait;
use Tests\DrdPlus\CalculatorSkeleton\CalculatorSkeletonExceptionsHierarchyTest;

class AttackSkeletonExceptionsHierarchyTest extends CalculatorSkeletonExceptionsHierarchyTest
{
    use AttackCalculatorTestTrait;

    protected function getTestedNamespace(): string
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

}
