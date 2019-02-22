<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Partials;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Dirs;
use DrdPlus\Tests\CalculatorSkeleton\Partials\CalculatorContentTestTrait;

/**
 * @method HtmlHelper getHtmlHelper
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
        return new HtmlHelper($dirs ?? $this->getDirs(), $inDevMode, $inForcedProductionMode, $shouldHideCovered);
    }

    protected function isAttackSkeletonChecked(): bool
    {
        return $this->isSkeletonChecked($this->getAttackSkeletonProjectRoot());
    }

    private function getAttackSkeletonProjectRoot(): string
    {
        return __DIR__ . '/../../../..';
    }
}