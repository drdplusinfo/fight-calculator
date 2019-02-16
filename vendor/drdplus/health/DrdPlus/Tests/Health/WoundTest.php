<?php
namespace DrdPlus\Tests\Health;

use DrdPlus\Codes\Body\OrdinaryWoundOriginCode;
use DrdPlus\Codes\Body\SeriousWoundOriginCode;
use DrdPlus\Health\Health;
use DrdPlus\Health\PointOfWound;
use DrdPlus\Health\Wound;
use DrdPlus\Health\WoundSize;
use Granam\Tests\Tools\TestWithMockery;

abstract class WoundTest extends TestWithMockery
{
    /**
     * @test
     * @return Wound
     */
    public function I_can_use_it(): Wound
    {
        $wound = $this->createWound(
            $health = $this->createHealth(),
            new WoundSize($woundSizeValue = 3),
            $woundOrigin = SeriousWoundOriginCode::getMechanicalCutWoundOrigin()
        );
        self::assertSame($health, $wound->getHealth());
        self::assertSame($woundSizeValue, $wound->getValue());
        $this->assertIsSeriousAsExpected($wound);
        if ($wound->isSerious()) {
            self::assertSame($woundOrigin, $wound->getWoundOriginCode());
        } else {
            self::assertSame(OrdinaryWoundOriginCode::getIt(), $wound->getWoundOriginCode());
        }
        self::assertFalse($wound->isHealed(), "Wound with {$woundSizeValue} should not be identified as healed");
        $pointsOfWound = $wound->getPointsOfWound();
        self::assertCount($woundSizeValue, $pointsOfWound);
        foreach ($pointsOfWound as $pointOfWound) {
            self::assertInstanceOf(PointOfWound::class, $pointOfWound);
        }
        self::assertTrue($wound->isFresh());
        self::assertFalse($wound->isOld());
        $wound->setOld();
        self::assertFalse($wound->isFresh());
        self::assertTrue($wound->isOld());
        self::assertSame('3', (string)$wound);
        self::assertNotSame($wound->isSerious(), $wound->isOrdinary(), 'Both can not be both serious as well as ordinary');

        return $wound;
    }

    /**
     * @param Health $health
     * @param WoundSize $woundSize
     * @param SeriousWoundOriginCode $seriousWoundOriginCode
     * @return Wound
     */
    abstract protected function createWound(Health $health, WoundSize $woundSize, SeriousWoundOriginCode $seriousWoundOriginCode): Wound;

    /**
     * @param bool $openForNewWounds
     * @return \Mockery\MockInterface|Health
     */
    private function createHealth($openForNewWounds = true)
    {
        $health = $this->mockery(Health::class);
        $health->shouldReceive('isOpenForNewWound')
            ->andReturn($openForNewWounds);

        return $health;
    }

    /**
     * @param Wound $wound
     */
    abstract protected function assertIsSeriousAsExpected(Wound $wound);

    /**
     * @param Wound $wound
     */
    abstract protected function assertIsOrdinaryAsExpected(Wound $wound);

    /**
     * @test
     */
    public function I_can_heal_it_both_partially_and_fully(): void
    {
        $wound = $this->createWound(
            $health = $this->createHealth(),
            new WoundSize($woundSizeValue = 3),
            $elementalWoundOrigin = SeriousWoundOriginCode::getElementalWoundOrigin()
        );
        self::assertSame($woundSizeValue, $wound->getValue(), 'Expected same value as created with');
        self::assertCount($woundSizeValue, $wound->getPointsOfWound());
        self::assertFalse($wound->isHealed());
        $this->assertIsSeriousAsExpected($wound);
        $this->assertIsOrdinaryAsExpected($wound);
        self::assertFalse($wound->isOld());
        self::assertTrue($wound->isFresh());

        self::assertSame(1, $wound->heal(1), 'Expected reported healed value to be 1');
        self::assertSame(2, $wound->getValue(), 'Expected one point of wound to be already healed');
        self::assertCount(2, $wound->getPointsOfWound());
        self::assertFalse($wound->isHealed());
        self::assertTrue($wound->isOld(), 'Wound should become "old" after any heal attempt');
        self::assertFalse($wound->isFresh(), 'Wound should not be "fresh" after any heal attempt');

        self::assertSame(2, $wound->heal(999), 'Expected reported healed value to be the remaining value, 2');
        self::assertEmpty($wound->getPointsOfWound());
        self::assertTrue($wound->isHealed());
        self::assertTrue($wound->isOld(), 'Wound should become "old" after any heal attempt');
        self::assertFalse($wound->isFresh(), 'Wound should not be "fresh" after any heal attempt');
    }

    /**
     * @test
     */
    public function I_can_create_wound_with_zero_value(): void
    {
        $wound = $this->createWound(
            $this->createHealth(),
            new WoundSize(0),
            SeriousWoundOriginCode::getMechanicalCrushWoundOrigin()
        );
        self::assertSame(0, $wound->getValue());
        self::assertTrue($wound->isHealed());
        self::assertTrue($wound->isFresh());
        self::assertFalse($wound->isOld());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\WoundHasToBeCreatedByHealthItself
     */
    public function I_can_not_create_wound_directly(): void
    {
        $this->createWound(
            $this->createHealth(false /* not open for new wounds */),
            new WoundSize(1),
            SeriousWoundOriginCode::getMechanicalCrushWoundOrigin()
        );
    }
}