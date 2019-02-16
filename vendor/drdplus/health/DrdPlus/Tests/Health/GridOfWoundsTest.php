<?php
namespace DrdPlus\Tests\Health;

use DrdPlus\Health\GridOfWounds;
use DrdPlus\Health\Health;
use DrdPlus\Health\PointOfWound;
use DrdPlus\Health\Wound;
use DrdPlus\Health\WoundSize;
use DrdPlus\Properties\Derived\WoundBoundary;
use Granam\Tests\Tools\TestWithMockery;

class GridOfWoundsTest extends TestWithMockery
{
    /**
     * @var PointOfWound
     */
    private static $pointOfWound;

    protected function setUp(): void
    {
        self::$pointOfWound = $this->mockery(PointOfWound::class);
    }

    /**
     * @test
     */
    public function I_can_get_maximum_of_wounds_per_row(): void
    {
        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(123, $gridOfWounds->getWoundsPerRowMaximum($this->createWoundBoundary(123)));
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|WoundBoundary
     */
    private function createWoundBoundary(int $value)
    {
        $woundBoundary = $this->mockery(WoundBoundary::class);
        $woundBoundary->shouldReceive('getValue')
            ->andReturn($value);

        return $woundBoundary;
    }

    /**
     * @param array|Wound[] $unhealedWounds
     * @return \Mockery\MockInterface|Health
     */
    private function createHealth(array $unhealedWounds = []): Health
    {
        $health = $this->mockery(Health::class);
        $health->shouldReceive('getUnhealedWounds')
            ->andReturn($unhealedWounds);
        $health->shouldReceive('getUnhealedWoundsSum')
            ->andReturn(
                (int)\array_sum(\array_map(
                    function (Wound $wound) {
                        return \count($wound->getPointsOfWound());
                    },
                    $unhealedWounds
                ))
            );

        return $health;
    }

    /**
     * @param array|int[] $woundValues
     * @return Wound[]
     */
    private function createWounds(array $woundValues): array
    {
        $wounds = [];
        foreach ($woundValues as $woundValue) {
            $wound = $this->mockery(Wound::class);
            $wound->shouldReceive('getPointsOfWound')
                ->andReturn($this->createPointsOfWound($woundValue));

            $wounds[] = $wound;
        }

        return $wounds;
    }

    /**
     * @param int $woundValue
     * @return array|PointOfWound[]
     */
    private function createPointsOfWound($woundValue): array
    {
        $pointsOfWound = [];
        for ($pointRank = 1; $pointRank <= $woundValue; $pointRank++) {
            $pointsOfWound[] = self::$pointOfWound;
        }
        return $pointsOfWound;
    }

    /**
     * @test
     */
    public function I_can_get_calculated_filled_half_rows_for_given_wound_value(): void
    {
        // limit of wounds divisible by two (odd)
        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            6,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(492), $this->createWoundBoundary(124)),
            'Expected cap of half rows'
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            0,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(0), $this->createWoundBoundary(124)),
            'Expected no half row'
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            1,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(11), $this->createWoundBoundary(22)),
            'Expected two half rows'
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            5,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(10), $this->createWoundBoundary(4)),
            'Expected five half rows'
        );

        // even limit of wounds
        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            6,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(999), $this->createWoundBoundary(111)),
            'Expected cap of half rows'
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            0,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(5), $this->createWoundBoundary(333)),
            'Expected no half row'
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            0,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(6), $this->createWoundBoundary(13)),
            '"first" half of row should be rounded up'
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            1,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(7), $this->createWoundBoundary(13))
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            2,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(13), $this->createWoundBoundary(13)),
            'Same value as row of wound should take two halves of such value even if even'
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            2,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(7), $this->createWoundBoundary(5)),
            '"third" half or row should be rounded up'
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            3,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(8), $this->createWoundBoundary(5))
        );

        $gridOfWounds = new GridOfWounds($this->createHealth([] /* no wounds*/));
        self::assertSame(
            4,
            $gridOfWounds->calculateFilledHalfRowsFor(WoundSize::createIt(10), $this->createWoundBoundary(5))
        );
    }

    /**
     * @test
     */
    public function I_can_get_number_of_filled_rows(): void
    {
        $gridOfWounds = new GridOfWounds($this->createHealth($this->createWounds([3, 1])));
        self::assertSame(
            0,
            $gridOfWounds->getNumberOfFilledRows($this->createWoundBoundary(23))
        );

        $gridOfWounds = new GridOfWounds($this->createHealth($this->createWounds([1, 21, 5, 14])));
        self::assertSame(
            1,
            $gridOfWounds->getNumberOfFilledRows($this->createWoundBoundary(23))
        );

        $gridOfWounds = new GridOfWounds($this->createHealth($this->createWounds([1, 21, 10, 14])));
        self::assertSame(
            2,
            $gridOfWounds->getNumberOfFilledRows($this->createWoundBoundary(23))
        );

        $gridOfWounds = new GridOfWounds($this->createHealth($this->createWounds([1, 21, 10, 14, 500])));
        self::assertSame(
            3,
            $gridOfWounds->getNumberOfFilledRows($this->createWoundBoundary(23)),
            'Maximum of rows should not exceed 3'
        );
    }
}