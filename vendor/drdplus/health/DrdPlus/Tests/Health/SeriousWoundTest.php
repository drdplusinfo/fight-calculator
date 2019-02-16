<?php
namespace DrdPlus\Tests\Health;

use DrdPlus\Codes\Body\SeriousWoundOriginCode;
use DrdPlus\Health\Health;
use DrdPlus\Health\SeriousWound;
use DrdPlus\Health\Wound;
use DrdPlus\Health\WoundSize;

class SeriousWoundTest extends WoundTest
{
    /**
     * @param Health $health
     * @param WoundSize $woundSize
     * @param SeriousWoundOriginCode $seriousWoundOriginCode
     * @return SeriousWound
     */
    protected function createWound(Health $health, WoundSize $woundSize, SeriousWoundOriginCode $seriousWoundOriginCode): Wound
    {
        return new SeriousWound($health, $woundSize, $seriousWoundOriginCode);
    }

    /**
     * @param Wound $wound
     */
    protected function assertIsSeriousAsExpected(Wound $wound): void
    {
        self::assertInstanceOf(SeriousWound::class, $wound);
        self::assertTrue($wound->isSerious(), \get_class($wound) . ' should be serious');
    }

    /**
     * @param Wound $wound
     */
    protected function assertIsOrdinaryAsExpected(Wound $wound): void
    {
        self::assertInstanceOf(SeriousWound::class, $wound);
        self::assertFalse($wound->isOrdinary(), \get_class($wound) . ' should not be ordinary');
    }

}
