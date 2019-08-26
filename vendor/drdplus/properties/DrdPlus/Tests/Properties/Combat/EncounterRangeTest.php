<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat;

use DrdPlus\Properties\Combat\EncounterRange;
use DrdPlus\Tests\Properties\Combat\Partials\AbstractRangeTest;

class EncounterRangeTest extends AbstractRangeTest
{
    /**
     * @test
     */
    public function I_can_get_property_easily()
    {
        $encounterRange = EncounterRange::getIt(1234);
        self::assertInstanceOf(EncounterRange::class, $encounterRange);
        self::assertSame(1234, $encounterRange->getValue());
    }

    /**
     * @param int $value
     * @return EncounterRange
     */
    protected function createRangeSut($value): EncounterRange
    {
        return EncounterRange::getIt($value);
    }
}