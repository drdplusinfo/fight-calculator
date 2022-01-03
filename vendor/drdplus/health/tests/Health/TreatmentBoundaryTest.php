<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health;

use DrdPlus\Health\TreatmentBoundary;
use PHPUnit\Framework\TestCase;

class TreatmentBoundaryTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_create_treatment_boundary(): void
    {
        $treatmentBoundary = TreatmentBoundary::getIt($value = 0);
        self::assertSame($value, $treatmentBoundary->getValue());
    }

    /**
     * @test
     */
    public function I_am_stopped_by_specific_exception_on_invalid_value(): void
    {
        $this->expectException(\DrdPlus\Health\Exceptions\TreatmentBoundaryCanNotBeNegative::class);
        $this->expectExceptionMessageMatches('~Why you ask me?~');
        TreatmentBoundary::getIt('Why you ask me?');
    }

    /**
     * @test
     */
    public function I_can_not_use_negative_value(): void
    {
        $this->expectException(\DrdPlus\Health\Exceptions\TreatmentBoundaryCanNotBeNegative::class);
        $this->expectExceptionMessageMatches('~-1~');
        TreatmentBoundary::getIt(-1);
    }
}
