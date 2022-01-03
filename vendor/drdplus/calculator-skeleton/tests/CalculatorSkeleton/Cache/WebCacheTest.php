<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton\Cache;

use DrdPlus\RulesSkeleton\Cache\WebCache;
use Tests\DrdPlus\CalculatorSkeleton\Partials\CalculatorContentTestTrait;

class WebCacheTest extends \Tests\DrdPlus\RulesSkeleton\Cache\WebCacheTest
{
    use CalculatorContentTestTrait;

    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~\\\Tests(.+)Test$~'): string
    {
        return WebCache::class;
    }

}
