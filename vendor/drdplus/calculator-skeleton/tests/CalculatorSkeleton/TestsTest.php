<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton;

class TestsTest extends \Tests\DrdPlus\RulesSkeleton\TestsTest
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function All_rules_skeleton_tests_are_used(): void
    {
        $reflectionClass = new \ReflectionClass(parent::class);
        $rulesSkeletonDir = \dirname($reflectionClass->getFileName());
        foreach ($this->getClassesFromDir($rulesSkeletonDir) as $rulesSkeletonTestClass) {
            if (is_a($rulesSkeletonTestClass, \Throwable::class, true)) {
                continue;
            }
            $rulesSkeletonTestClassReflection = new \ReflectionClass($rulesSkeletonTestClass);
            if ($rulesSkeletonTestClassReflection->isAbstract()
                || $rulesSkeletonTestClassReflection->isInterface()
                || $rulesSkeletonTestClassReflection->isTrait()
            ) {
                continue;
            }
            $expectedCalculatorTestClass = str_replace('\\RulesSkeleton', '\\CalculatorSkeleton', $rulesSkeletonTestClass);
            self::assertTrue(
                class_exists($expectedCalculatorTestClass),
                "Missing test class {$expectedCalculatorTestClass} adopted from rules skeleton test class {$rulesSkeletonTestClass}"
            );
            self::assertTrue(
                is_a($expectedCalculatorTestClass, $rulesSkeletonTestClass, true),
                "$expectedCalculatorTestClass should be a child of $rulesSkeletonTestClass"
            );
        }
    }

    /**
     * @test
     */
    public function Test_classes_are_unique_accross_namespaces()
    {
        $testsClasses = $this->getClassesFromDir(DRD_PLUS_TESTS_ROOT);
        $testsClassNamesWithoutNamespace = array_map(
            static fn(string $className) => substr($className, strrpos($className, '\\')),
            $testsClasses
        );
        $nonUnique = [];
        $occurrences = [];
        foreach ($testsClassNamesWithoutNamespace as $testsClassNameWithoutNamespace) {
            $occurrences[$testsClassNameWithoutNamespace] = ($occurrences[$testsClassNameWithoutNamespace] ?? 0) + 1;
            if ($occurrences[$testsClassNameWithoutNamespace] === 2) {
                $nonUnique[] = $testsClassNameWithoutNamespace;
            }
        }
        self::assertSame([], $nonUnique, 'All test classes should be unique to avoid their mistaken duplicity');
    }
}
