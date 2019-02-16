<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

class TestsTest extends \DrdPlus\Tests\RulesSkeleton\TestsTest
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function All_calculator_skeleton_tests_are_used(): void
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $calculatorSkeletonDir = \dirname($reflectionClass->getFileName());
        foreach ($this->getClassesFromDir($calculatorSkeletonDir) as $calculatorSkeletonTestClass) {
            if (\is_a($calculatorSkeletonTestClass, \Throwable::class, true)) {
                continue;
            }
            $calculatorSkeletonTestClassReflection = new \ReflectionClass($calculatorSkeletonTestClass);
            if ($calculatorSkeletonTestClassReflection->isAbstract()
                || $calculatorSkeletonTestClassReflection->isInterface()
                || $calculatorSkeletonTestClassReflection->isTrait()
            ) {
                continue;
            }
            $expectedCalculatorTestClass = \str_replace('\CalculatorSkeleton', '\AttackSkeleton', $calculatorSkeletonTestClass);
            self::assertTrue(
                \class_exists($expectedCalculatorTestClass),
                "Missing test class {$expectedCalculatorTestClass} adopted from calculator skeleton test class {$calculatorSkeletonTestClass}"
            );
            self::assertTrue(
                \is_a($expectedCalculatorTestClass, $calculatorSkeletonTestClass, true),
                "$expectedCalculatorTestClass should be a child of $calculatorSkeletonTestClass"
            );
        }
    }
}