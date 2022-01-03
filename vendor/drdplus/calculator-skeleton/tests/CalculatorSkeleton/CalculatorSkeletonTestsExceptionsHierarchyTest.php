<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton;

class CalculatorSkeletonTestsExceptionsHierarchyTest extends \Tests\DrdPlus\RulesSkeleton\RulesSkeletonTestsExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace(): string
    {
        return __NAMESPACE__;
    }

}
