<?php declare(strict_types=1);

namespace Tests\DrdPlus\AttackSkeleton;

use Tests\DrdPlus\AttackSkeleton\Partials\AttackCalculatorTestTrait;

class TestsTest extends \Tests\DrdPlus\CalculatorSkeleton\TestsTest
{
    use AttackCalculatorTestTrait;

    /**
     * @test
     * @throws \ReflectionException
     */
    public function All_rules_skeleton_tests_are_used(): void
    {
        $reflectionClass = new \ReflectionClass(parent::class);
        $calculatorSkeletonDir = dirname($reflectionClass->getFileName());
        foreach ($this->getClassesFromDir($calculatorSkeletonDir) as $calculatorSkeletonTestClass) {
            if (is_a($calculatorSkeletonTestClass, \Throwable::class, true)) {
                continue;
            }
            $calculatorSkeletonTestClassReflection = new \ReflectionClass($calculatorSkeletonTestClass);
            if ($calculatorSkeletonTestClassReflection->isAbstract()
                || $calculatorSkeletonTestClassReflection->isInterface()
                || $calculatorSkeletonTestClassReflection->isTrait()
            ) {
                continue;
            }
            $expectedAttackTestClass = str_replace('\\CalculatorSkeleton', '\\AttackSkeleton', $calculatorSkeletonTestClass);
            self::assertTrue(
                class_exists($expectedAttackTestClass),
                "Missing test class {$expectedAttackTestClass} adopted from rules skeleton test class {$calculatorSkeletonTestClass}"
            );
            self::assertTrue(
                is_a($expectedAttackTestClass, $calculatorSkeletonTestClass, true),
                "$expectedAttackTestClass should be a child of $calculatorSkeletonTestClass"
            );

            $attackTestClassReflection = new \ReflectionClass($expectedAttackTestClass);
            self::assertContains(
                AttackCalculatorTestTrait::class,
                $attackTestClassReflection->getTraitNames(),
                sprintf("Adopted test '%s' should has attack trait '%s'", $expectedAttackTestClass, AttackCalculatorTestTrait::class)
            );
        }
    }

    protected function getTestingClassesWithoutSut(): array
    {
        $parentTestingClassesWithoutSut = parent::getTestingClassesWithoutSut();
        $testingClassesWithoutSut = $parentTestingClassesWithoutSut;
        foreach ($parentTestingClassesWithoutSut as $testClassWithoutSut) {
            $adoptedTestClassWithoutSut = str_replace('RulesSkeleton', 'AttackSkeleton', $testClassWithoutSut);
            if (class_exists($adoptedTestClassWithoutSut)) {
                $testingClassesWithoutSut[] = $adoptedTestClassWithoutSut;
            }
            $adoptedTestClassWithoutSut = str_replace('\RulesSkeleton\\', '\AttackSkeleton\\', $testClassWithoutSut);
            if (class_exists($adoptedTestClassWithoutSut)) {
                $testingClassesWithoutSut[] = $adoptedTestClassWithoutSut;
            }
        }
        return $testingClassesWithoutSut;
    }

    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~\\\Tests(.+)Test$~'): string
    {
        $sutClass = parent::getSutClass($sutTestClass, $regexp);
        if (class_exists($sutClass)) {
            return $sutClass;
        }
        $sutClass = str_replace('\AttackSkeleton\\', '\CalculatorSkeleton\\', $sutClass);
        if (class_exists($sutClass)) {
            return $sutClass;
        }
        return str_replace('\CalculatorSkeleton\\', '\RulesSkeleton\\', $sutClass);
    }
}
