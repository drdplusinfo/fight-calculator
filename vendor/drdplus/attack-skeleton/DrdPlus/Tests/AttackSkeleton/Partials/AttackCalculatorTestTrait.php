<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Partials;

use DrdPlus\AttackSkeleton\AttackServicesContainer;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\CalculatorSkeleton\CalculatorApplication;
use DrdPlus\CalculatorSkeleton\CalculatorConfiguration;
use DrdPlus\RulesSkeleton\Dirs;
use DrdPlus\Tests\CalculatorSkeleton\Partials\CalculatorContentTestTrait;

/**
 * @method HtmlHelper getHtmlHelper()
 * @method CalculatorConfiguration getConfiguration(Dirs $dirs = null)
 * @method static assertTrue($value, $message = '')
 * @method static assertFalse($value, $message = '')
 * @method static assertSame($expected, $actual, $message = '')
 * @method static assertNotSame($expected, $actual, $message = '')
 * @method static assertNotEmpty($value, $message = '')
 * @method static fail($message)
 */
trait AttackCalculatorTestTrait
{
    use CalculatorContentTestTrait;

    /**
     * @param Dirs|null $dirs
     * @param bool $inDevMode
     * @param bool $inForcedProductionMode
     * @param bool $shouldHideCovered
     * @return \DrdPlus\RulesSkeleton\HtmlHelper|\DrdPlus\AttackSkeleton\HtmlHelper
     */
    protected function createHtmlHelper(
        Dirs $dirs = null,
        bool $inForcedProductionMode = false,
        bool $inDevMode = false,
        bool $shouldHideCovered = false
    ): \DrdPlus\RulesSkeleton\HtmlHelper
    {
        return new HtmlHelper(
            $dirs ?? $this->getDirs(),
            $this->getEnvironment(),
            $inDevMode,
            $inForcedProductionMode,
            $shouldHideCovered
        );
    }

    protected function isAttackSkeletonChecked(): bool
    {
        return $this->isSkeletonChecked($this->getAttackSkeletonProjectRoot());
    }

    private function getAttackSkeletonProjectRoot(): string
    {
        return __DIR__ . '/../../../..';
    }

    protected function isSkeletonChecked(string $skeletonDocumentRoot = null): bool
    {
        $documentRootRealPath = \realpath($this->getProjectRoot());
        self::assertNotEmpty($documentRootRealPath, 'Can not find out real path of document root ' . \var_export($this->getProjectRoot(), true));
        $skeletonRootRealPath = \realpath($skeletonDocumentRoot ?? $this->getAttackSkeletonProjectRoot());
        self::assertNotEmpty($skeletonRootRealPath, 'Can not find out real path of skeleton root ' . \var_export($skeletonRootRealPath, true));

        return $documentRootRealPath === $skeletonRootRealPath;
    }

    protected function getServicesContainerClass(): string
    {
        return defined('DRD_PLUS_SERVICES_CONTAINER_CLASS')
            ? DRD_PLUS_SERVICES_CONTAINER_CLASS
            : AttackServicesContainer::class;
    }

    /**
     * @return string|CalculatorConfiguration
     */
    protected function getConfigurationClass(): string
    {
        return defined('DRD_PLUS_CONFIGURATION_CLASS')
            ? DRD_PLUS_CONFIGURATION_CLASS
            : CalculatorConfiguration::class;
    }

    /**
     * @return string|CalculatorApplication
     */
    protected function getRulesApplicationClass(): string
    {
        return defined('DRD_PLUS_APPLICATION_CLASS')
            ? DRD_PLUS_APPLICATION_CLASS
            : CalculatorApplication::class;
    }
}