<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Psychical;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Psychical\MapsDrawing;
use DrdPlus\Skills\Psychical\RollsOn\MapQuality;
use DrdPlus\Skills\Psychical\RollsOn\RollOnMapUsage;

class MapsDrawingTest extends WithBonusFromPsychicalTest
{
    /**
     * @param int $skillRankValue
     * @return int
     */
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return 2 * $skillRankValue;
    }

    /**
     * @test
     */
    public function I_can_get_quality_of_new_map()
    {
        $mapsDrawing = new MapsDrawing($this->createProfessionLevel());
        $knack = $this->createKnack(123);
        $roll2D6DrdPlus = $this->createRoll2d6DrdPlus(465);
        $mapQuality = $mapsDrawing->getCreatedMapQuality($knack, $roll2D6DrdPlus);
        self::assertInstanceOf(MapQuality::class, $mapQuality);
        self::assertEquals(new MapQuality($knack, $mapsDrawing, $roll2D6DrdPlus), $mapQuality);
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Knack
     */
    private function createKnack($value)
    {
        $knack = $this->mockery(Knack::class);
        $knack->shouldReceive('getValue')
            ->andReturn($value);

        return $knack;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Roll2d6DrdPlus
     */
    private function createRoll2d6DrdPlus($value)
    {
        $knack = $this->mockery(Roll2d6DrdPlus::class);
        $knack->shouldReceive('getValue')
            ->andReturn($value);

        return $knack;
    }

    /**
     * @test
     */
    public function I_can_get_roll_on_map_usage()
    {
        $mapsDrawing = new MapsDrawing($this->createProfessionLevel());
        $intelligence = $this->createIntelligence(123);
        $roll2D6DrdPlus = $this->createRoll2d6DrdPlus(465);
        $rollOnMapUsage = $mapsDrawing->getRollOnMapUsage($intelligence, $roll2D6DrdPlus);
        self::assertInstanceOf(RollOnMapUsage::class, $rollOnMapUsage);
        self::assertEquals(new RollOnMapUsage($intelligence, $mapsDrawing, $roll2D6DrdPlus), $rollOnMapUsage);
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Intelligence
     */
    private function createIntelligence($value)
    {
        $intelligence = $this->mockery(Intelligence::class);
        $intelligence->shouldReceive('getValue')
            ->andReturn($value);

        return $intelligence;
    }

    /**
     * @test
     * @dataProvider provideMapQualityAndRollOnMapUsageWithExpectedResultingBonus
     * @param int $mapQualityValue
     * @param int $rollOnMapUsage
     * @param int $expectedBonusToNavigation
     */
    public function I_can_get_bonus_on_navigation(
        int $mapQualityValue,
        int $rollOnMapUsage,
        int $expectedBonusToNavigation
    )
    {
        $mapsDrawing = new MapsDrawing($this->createProfessionLevel());
        self::assertSame(
            $expectedBonusToNavigation,
            $mapsDrawing->getBonusToNavigation(
                $this->createMapQuality($mapQualityValue),
                $this->createRollOnMapUsage($rollOnMapUsage)
            )
        );
    }

    public function provideMapQualityAndRollOnMapUsageWithExpectedResultingBonus()
    {
        return [
            [13, 57, 2],
            [25, 21, 4],
        ];
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|MapQuality
     */
    private function createMapQuality($value)
    {
        $mapQuality = $this->mockery(MapQuality::class);
        $mapQuality->shouldReceive('getValue')
            ->andReturn($value);

        return $mapQuality;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|RollOnMapUsage
     */
    private function createRollOnMapUsage($value)
    {
        $rollOnMapUsage = $this->mockery(RollOnMapUsage::class);
        $rollOnMapUsage->shouldReceive('getValue')
            ->andReturn($value);

        return $rollOnMapUsage;
    }

}