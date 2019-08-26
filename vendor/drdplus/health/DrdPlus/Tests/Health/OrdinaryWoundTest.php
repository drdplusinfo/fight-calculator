<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health;

use DrdPlus\Codes\Body\SeriousWoundOriginCode;
use DrdPlus\Health\Health;
use DrdPlus\Health\OrdinaryWound;
use DrdPlus\Health\Wound;
use DrdPlus\Health\WoundSize;

class OrdinaryWoundTest extends WoundTest
{
    /**
     * @param Health $health
     * @param WoundSize $woundSize
     * @param SeriousWoundOriginCode $seriousWoundOriginCode
     * @return OrdinaryWound
     */
    protected function createWound(Health $health, WoundSize $woundSize, SeriousWoundOriginCode $seriousWoundOriginCode): Wound
    {
        return new OrdinaryWound($health, $woundSize);
    }

    /**
     * @param Wound $wound
     */
    protected function assertIsSeriousAsExpected(Wound $wound): void
    {
        self::assertInstanceOf(OrdinaryWound::class, $wound);
        self::assertFalse($wound->isSerious(), \get_class($wound) . ' should not be serious');
    }

    /**
     * @param Wound $wound
     */
    protected function assertIsOrdinaryAsExpected(Wound $wound): void
    {
        self::assertInstanceOf(OrdinaryWound::class, $wound);
        self::assertTrue($wound->isOrdinary(), \get_class($wound) . ' should be ordinary');
    }
}