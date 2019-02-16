<?php
namespace DrdPlus\Health\Inflictions;

use DrdPlus\Codes\Units\TimeUnitCode;
use DrdPlus\Health\Health;
use DrdPlus\Lighting\Glare;
use DrdPlus\Tables\Measurements\Time\Time;
use DrdPlus\Tables\Measurements\Time\TimeTable;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class GlaredTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideMalusAndShined
     * @param int $malus
     * @param bool $isShined
     */
    public function I_can_create_it_from_glare(int $malus, bool $isShined): void
    {
        $glared = Glared::createFromGlare($this->createGlare($malus, $isShined), $health = new Health());
        self::assertSame($malus, $glared->getCurrentMalus());
        self::assertSame($isShined, $glared->isShined());
        self::assertSame(!$isShined, $glared->isBlinded());
        self::assertSame($health, $glared->getHealth());
        self::assertSame(0, $glared->getGettingUsedToForRounds());
    }

    public function provideMalusAndShined(): array
    {
        return [
            [123, true],
            [789, false],
        ];
    }

    /**
     * @param int $malus
     * @param bool $isShined
     * @return \Mockery\MockInterface|Glare
     */
    private function createGlare($malus, $isShined)
    {
        $glare = $this->mockery(Glare::class);
        $glare->shouldReceive('getMalus')
            ->andReturn($malus);
        $glare->shouldReceive('isShined')
            ->andReturn($isShined);

        return $glare;
    }

    /**
     * @test
     */
    public function I_can_create_it_without_glare_at_all(): void
    {
        $glared = Glared::createWithoutGlare($health = new Health());
        self::assertSame(0, $glared->getCurrentMalus());
        self::assertSame($health, $glared->getHealth());
        self::assertSame(0, $glared->getGettingUsedToForRounds());
    }

    /**
     * @test
     */
    public function I_can_lower_malus_by_getting_used_to_shine(): void
    {
        $glared = Glared::createFromGlare($this->createGlare(-15, true), new Health());

        self::assertSame(-15, $glared->getCurrentMalus());
        self::assertTrue($glared->isShined()); // shined means one round to remove one malus point
        self::assertSame(0, $glared->getGettingUsedToForRounds());

        $timeTable = new TimeTable();
        $glared->setGettingUsedToForTime(new Time(1, TimeUnitCode::ROUND, $timeTable));
        self::assertSame(-14, $glared->getCurrentMalus());
        self::assertSame(1, $glared->getGettingUsedToForRounds());

        $glared->setGettingUsedToForTime(new Time(5, TimeUnitCode::ROUND, $timeTable));
        self::assertSame(-10, $glared->getCurrentMalus());
        self::assertSame(5, $glared->getGettingUsedToForRounds());

        $glared->setGettingUsedToForTime(new Time(999, TimeUnitCode::ROUND, $timeTable));
        self::assertSame(0, $glared->getCurrentMalus());
        self::assertSame(15, $glared->getGettingUsedToForRounds());
    }

    /**
     * @test
     */
    public function I_can_get_used_to_shine_by_long_waiting(): void
    {
        $glared = Glared::createFromGlare($this->createGlare(-50, true), new Health());

        self::assertSame(-50, $glared->getCurrentMalus());
        self::assertTrue($glared->isShined()); // shined means one round to remove one malus point
        self::assertSame(0, $glared->getGettingUsedToForRounds());

        $timeTable = new TimeTable();
        $glared->setGettingUsedToForTime(new Time(1, TimeUnitCode::HOUR, $timeTable));
        self::assertSame(0, $glared->getCurrentMalus());
        self::assertSame(50, $glared->getGettingUsedToForRounds());
    }

    /**
     * @test
     */
    public function I_can_lower_malus_by_getting_used_to_darkness(): void
    {
        $glared = Glared::createFromGlare($this->createGlare(-36, false), new Health());

        self::assertSame(-36, $glared->getCurrentMalus());
        self::assertTrue($glared->isBlinded()); // blinded means ten rounds to remove one malus point
        self::assertSame(0, $glared->getGettingUsedToForRounds());

        $timeTable = new TimeTable();
        $glared->setGettingUsedToForTime(new Time(1, TimeUnitCode::ROUND, $timeTable));
        self::assertSame(-36, $glared->getCurrentMalus());
        self::assertSame(1, $glared->getGettingUsedToForRounds());

        $glared->setGettingUsedToForTime(new Time(9, TimeUnitCode::ROUND, $timeTable));
        self::assertSame(-36, $glared->getCurrentMalus());
        self::assertSame(9, $glared->getGettingUsedToForRounds());

        $glared->setGettingUsedToForTime(new Time(10, TimeUnitCode::ROUND, $timeTable));
        self::assertSame(-35, $glared->getCurrentMalus());
        self::assertSame(10, $glared->getGettingUsedToForRounds());

        $glared->setGettingUsedToForTime(new Time(359, TimeUnitCode::ROUND, $timeTable));
        self::assertSame(-1, $glared->getCurrentMalus());
        self::assertSame(359, $glared->getGettingUsedToForRounds());

        $glared->setGettingUsedToForTime(new Time(800, TimeUnitCode::ROUND, $timeTable));
        self::assertSame(0, $glared->getCurrentMalus());
        self::assertSame(360, $glared->getGettingUsedToForRounds());
    }

    /**
     * @test
     */
    public function I_can_get_used_to_darkness_by_long_waiting(): void
    {
        $glared = Glared::createFromGlare($this->createGlare(-21 /* malus */, false), new Health());

        self::assertSame(-21, $glared->getCurrentMalus());
        self::assertTrue($glared->isBlinded()); // blinded means ten rounds to remove one malus point
        self::assertSame(0, $glared->getGettingUsedToForRounds());

        $timeTable = new TimeTable();
        $glared->setGettingUsedToForTime(new Time(1, TimeUnitCode::HOUR, $timeTable));
        self::assertSame(0, $glared->getCurrentMalus());
        self::assertSame(210, $glared->getGettingUsedToForRounds());
    }

    /**
     * @test
     */
    public function I_can_get_used_to_darkness_by_very_long_waiting_when_blinded(): void
    {
        $blinded = Glared::createFromGlare($this->createGlare($malus = -21, false /* not shined = blinded */), new Health());

        self::assertSame($malus, $blinded->getCurrentMalus());
        self::assertTrue($blinded->isBlinded()); // blinded means ten rounds to remove one malus point
        self::assertSame(0, $blinded->getGettingUsedToForRounds());

        $time = new Time(1, TimeUnitCode::YEAR, Tables::getIt()->getTimeTable());
        self::assertNull($time->findRounds(), 'Used time should be so long so it can not be expressed in rounds');
        $blinded->setGettingUsedToForTime($time);
        self::assertSame(0, $blinded->getCurrentMalus());
        self::assertSame(10 * \abs($malus), $blinded->getGettingUsedToForRounds());
    }

    /**
     * @test
     */
    public function I_can_get_used_to_darkness_by_very_long_waiting_when_shined(): void
    {
        $shined = Glared::createFromGlare($this->createGlare($malus = -1, true /* shined = not blinded */), new Health());

        self::assertSame($malus, $shined->getCurrentMalus());
        self::assertFalse($shined->isBlinded());
        self::assertTrue($shined->isShined()); // shined means one round to remove one malus point
        self::assertSame(0, $shined->getGettingUsedToForRounds());

        $time = new Time(1, TimeUnitCode::YEAR, Tables::getIt()->getTimeTable());
        self::assertNull($time->findRounds(), 'Used time should be so long so it can not be expressed in rounds');
        $shined->setGettingUsedToForTime($time);
        self::assertSame(0, $shined->getCurrentMalus());
        self::assertSame(\abs($malus), $shined->getGettingUsedToForRounds());
    }

}