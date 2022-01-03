<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton;

use DrdPlus\RulesSkeleton\CookiesService;

class CookiesServiceTest extends \Tests\DrdPlus\RulesSkeleton\CookiesServiceTest
{
    use Partials\CalculatorContentTestTrait;

    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~\\\Tests(.+)Test$~'): string
    {
        return CookiesService::class;
    }
}
