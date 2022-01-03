<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton;

class StandardModeTest extends \Tests\DrdPlus\RulesSkeleton\StandardModeTest
{
    use Partials\CalculatorContentTestTrait;

    /**
     * @test
     */
    public function I_get_notes_styled(): void
    {
        if (!$this->getTestsConfiguration()->hasNotes()) {
            self::assertFalse(false, 'No tables in calculator');

            return;
        }
        parent::I_get_notes_styled();
    }

}
