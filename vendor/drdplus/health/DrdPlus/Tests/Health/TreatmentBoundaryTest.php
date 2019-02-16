<?php
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
     * @expectedException \DrdPlus\Health\Exceptions\TreatmentBoundaryCanNotBeNegative
     * @expectedExceptionMessageRegExp ~Why you ask me?~
     */
    public function I_am_stopped_by_specific_exception_on_invalid_value(): void
    {
        TreatmentBoundary::getIt('Why you ask me?');
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\TreatmentBoundaryCanNotBeNegative
     * @expectedExceptionMessageRegExp ~-1~
     */
    public function I_can_not_use_negative_value(): void
    {
        TreatmentBoundary::getIt(-1);
    }
}