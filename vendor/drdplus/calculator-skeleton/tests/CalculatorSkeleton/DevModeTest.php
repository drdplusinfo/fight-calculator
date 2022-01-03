<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton;

/**
 * @backupGlobals enabled
 */
class DevModeTest extends \Tests\DrdPlus\RulesSkeleton\DevModeTest
{
    use Partials\CalculatorContentTestTrait;

    /**
     * @test
     */
    public function I_see_content_marked_by_development_classes(): void
    {
        self::assertFalse(false, 'Intended for rules skeleton only');
    }
}
