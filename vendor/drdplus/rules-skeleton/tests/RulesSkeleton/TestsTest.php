<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use Composer\Plugin\PluginInterface;
use DrdPlus\RulesSkeleton\InjectorComposerPlugin\SkeletonInjectorComposerPlugin;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Tests\DrdPlus\RulesSkeleton\Partials\TestsConfigurationReaderTest;

class TestsTest extends AbstractContentTest
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function Every_test_lives_in_drd_plus_tests_namespace(): void
    {
        $testsDir = $this->getTestsDir();
        $testClasses = $this->getClassesFromDir($testsDir);
        self::assertNotEmpty($testClasses, "No test classes found in {$testsDir}");
        foreach ($testClasses as $testClass) {
            self::assertStringStartsWith(
                'Tests\\DrdPlus',
                (new \ReflectionClass($testClass))->getNamespaceName(),
                "Class {$testClass} should be in DrdPlus\\Test namespace"
            );
        }
    }

    protected function getTestsDir(): string
    {
        $reflectionClass = new \ReflectionClass(static::class);
        return dirname($reflectionClass->getFileName());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function Every_test_reflects_test_class_namespace(): void
    {
        $testsDir = $this->getTestsDir();
        foreach ($this->getClassesFromDir($testsDir) as $testClass) {
            $this->Test_is_testing_something($testClass);
        }
    }

    /**
     * @param string $testClass
     * @throws \ReflectionException
     */
    protected function Test_is_testing_something(string $testClass)
    {
        $testingClassesWithoutSut = $this->getTestingClassesWithoutSut();
        $testClassReflection = new \ReflectionClass($testClass);
        if ($testClassReflection->isAbstract()
            || $testClassReflection->isInterface()
            || $testClassReflection->isTrait()
            || in_array($testClass, $testingClassesWithoutSut, true)
        ) {
            return;
        }
        if (is_a($testClass, SkeletonInjectorComposerPlugin::class, true)) {
            self::assertTrue(
                interface_exists(PluginInterface::class),
                "Composer package is required for this test, include it by\ncomposer require --dev composer/composer"
            );
        }
        $sutClass = static::getSutClass($testClass);
        $classExists = class_exists($sutClass);
        if (!$classExists) {
            foreach ($testingClassesWithoutSut as $testClassWithoutSut) {
                if (is_a($testClass, $testClassWithoutSut, true)) {
                    return; // some parent of test class is on white list
                }
            }
        }
        if (!$classExists) {
            $classExists = $this->isParentTestTestingSomeExistingClass($testClassReflection);
        }
        self::assertTrue(
            $classExists,
            "What is testing $testClass? Class $sutClass has not been found."
        );
    }

    private function isParentTestTestingSomeExistingClass(\ReflectionClass $testClassReflection): bool
    {
        $testParentClassReflection = $testClassReflection->getParentClass();
        if (!$testParentClassReflection || !preg_match('~Test$~', $testParentClassReflection->getName())) {
            return false;
        }
        $parentSutClass = static::getSutClass($testParentClassReflection->getName());
        if (class_exists($parentSutClass)) {
            return true;
        }
        return $this->isParentTestTestingSomeExistingClass($testParentClassReflection);
    }

    protected function getTestingClassesWithoutSut(): array
    {
        return [
            AnchorsTest::class,
            GraphicsTest::class,
            TestsConfigurationTest::class,
            RulesSkeletonExceptionsHierarchyTest::class,
            RulesSkeletonTestsExceptionsHierarchyTest::class,
            TablesTest::class,
            GoogleTest::class,
            WebContentVersionTest::class,
            ComposerConfigTest::class,
            TracyTest::class,
            TrialTest::class,
            PageTitleTest::class,
            StandardModeTest::class,
            self::class,
            static::class,
            CoveredPartsCanBeHiddenTest::class,
            GatewayPassingTest::class,
            ContactsContentTest::class,
            DevModeTest::class,
            CalculationsTest::class,
            SourceCodeLinksTest::class,
            TestsConfigurationReaderTest::class,
            TableOfContentsTest::class,
            GitTest::class,
        ];
    }

    protected function getClassesFromDir(string $dir): array
    {
        $classes = [];
        foreach (scandir($dir, SCANDIR_SORT_NONE) as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }
            $filename = $dir . '/' . $folder;
            if (!preg_match('~^[[:upper:]].*\.php$~', $folder)) {
                if (is_dir($filename)) {
                    foreach ($this->getClassesFromDir($filename) as $class) {
                        $classes[] = $class;
                    }
                }
                continue;
            }
            self::assertNotEmpty(
                preg_match('~/tests/(?<className>[A-Z][[:alnum:]]+/[^/].+)[.]php$~', $filename, $matches),
                "DrdPlus class name has not been determined from $filename"
            );
            $class = 'Tests\\DrdPlus\\' . str_replace('/', '\\', $matches['className']);
            self::assertTrue(
                class_exists($class) || trait_exists($class) || interface_exists($class),
                'Estimated test class does not exist: ' . $class
            );
            $classes[] = $class;
        }

        return $classes;
    }
}
