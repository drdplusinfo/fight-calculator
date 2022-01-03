<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton\Partials;

use DrdPlus\CalculatorSkeleton\CalculatorConfiguration;
use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\Configurations\ProjectUrlConfiguration;
use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\ServicesContainer;
use Tests\DrdPlus\CalculatorSkeleton\TestsConfiguration;

/**
 * @method CalculatorConfiguration getConfiguration(Dirs $dirs = null)
 * @method static assertTrue($value, $message = '')
 * @method static assertFalse($value, $message = '')
 * @method static assertSame($expected, $actual, $message = '')
 * @method static assertNotSame($expected, $actual, $message = '')
 * @method static assertNotEmpty($value, $message = '')
 * @method static fail($message)
 */
trait CalculatorContentTestTrait
{
    use CalculatorClassesTrait;

    protected function createServicesContainer(
        Configuration $configuration = null,
        Environment $environment = null,
        HtmlHelper $htmlHelper = null
    ): ServicesContainer
    {
        $servicesContainerClass = $this->getServicesContainerClass();
        return new $servicesContainerClass(
            $configuration ?? $this->getConfiguration(),
            $environment ?? $this->getEnvironment(),
            $htmlHelper ?? $this->getHtmlHelper()
        );
    }

    /**
     * @param string|null $class
     * @return TestsConfiguration|\Tests\DrdPlus\RulesSkeleton\TestsConfiguration
     */
    protected function getTestsConfiguration(string $class = null): \Tests\DrdPlus\RulesSkeleton\TestsConfiguration
    {
        static $testsConfiguration;
        if ($testsConfiguration === null) {
            $class = $class ?? $this->getTestsConfigurationClass();
            $testsConfiguration = $class::createFromYaml(
                \DRD_PLUS_TESTS_ROOT . '/tests_configuration.yml',
                $this->createHtmlHelper()
            );
        }

        return $testsConfiguration;
    }

    /**
     * @return TestsConfiguration|\Tests\DrdPlus\RulesSkeleton\TestsConfiguration
     */
    protected function getRulesSkeletonTestsConfiguration(): \Tests\DrdPlus\RulesSkeleton\TestsConfiguration
    {
        return parent::getTestsConfiguration();
    }

    protected function isSkeletonChecked(string $skeletonDocumentRoot = null): bool
    {
        $documentRootRealPath = \realpath($this->getProjectRoot());
        self::assertNotEmpty($documentRootRealPath, 'Can not find out real path of document root ' . \var_export($this->getProjectRoot(), true));
        $skeletonRootRealPath = \realpath($skeletonDocumentRoot ?? $this->getCalculatorSkeletonProjectRoot());
        self::assertNotEmpty($skeletonRootRealPath, 'Can not find out real path of skeleton root ' . \var_export($skeletonRootRealPath, true));

        return $documentRootRealPath === $skeletonRootRealPath;
    }

    protected function isCalculatorSkeletonChecked(): bool
    {
        return $this->isSkeletonChecked($this->getCalculatorSkeletonProjectRoot());
    }

    private function getCalculatorSkeletonProjectRoot(): string
    {
        return __DIR__ . '/../../..';
    }
}
