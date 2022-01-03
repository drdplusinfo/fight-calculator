<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton\Web\Gateway;

use Tests\DrdPlus\CalculatorSkeleton\Partials\CalculatorContentTestTrait;

class GatewayContentTest extends \Tests\DrdPlus\RulesSkeleton\Web\Gateway\GatewayContentTest
{
    use CalculatorContentTestTrait;

    /**
     * @test
     */
    public function Headings_do_not_have_anchors_to_self(): void
    {
        if (!$this->getTestsConfiguration()->hasProtectedAccess()) {
            self::assertFalse(false, 'No pass here');

            return;
        }
        parent::Headings_do_not_have_anchors_to_self();
    }

    /**
     * @test
     */
    public function Gateway_does_not_reset_requested_path_and_query(): void
    {
        if (!$this->getTestsConfiguration()->hasProtectedAccess()) {
            self::assertFalse(false, 'No pass here');

            return;
        }
        parent::Gateway_does_not_reset_requested_path_and_query();
    }
}
