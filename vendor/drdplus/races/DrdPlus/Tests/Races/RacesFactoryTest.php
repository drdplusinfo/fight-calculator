<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Races;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Races\Dwarfs\CommonDwarf;
use DrdPlus\Races\Dwarfs\MountainDwarf;
use DrdPlus\Races\Dwarfs\WoodDwarf;
use DrdPlus\Races\Elves\CommonElf;
use DrdPlus\Races\Elves\DarkElf;
use DrdPlus\Races\Elves\GreenElf;
use DrdPlus\Races\Hobbits\CommonHobbit;
use DrdPlus\Races\Humans\CommonHuman;
use DrdPlus\Races\Humans\Highlander;
use DrdPlus\Races\Krolls\CommonKroll;
use DrdPlus\Races\Krolls\WildKroll;
use DrdPlus\Races\Orcs\CommonOrc;
use DrdPlus\Races\Orcs\Goblin;
use DrdPlus\Races\Orcs\Skurut;
use DrdPlus\Races\RacesFactory;
use Granam\Tests\Tools\TestWithMockery;

class RacesFactoryTest extends TestWithMockery
{

    /**
     * @test
     * @dataProvider provideSubraceCodesAndClass
     * @param string $raceCodeValue
     * @param string $subraceCodeValue
     * @param string $expectedSubraceClass
     */
    public function I_can_create_subrace_by_its_codes($raceCodeValue, $subraceCodeValue, $expectedSubraceClass)
    {
        $raceCode = RaceCode::getIt($raceCodeValue);
        $subraceCode = SubRaceCode::getIt($subraceCodeValue);
        $subrace = RacesFactory::getSubRaceByCodes($raceCode, $subraceCode);
        self::assertInstanceOf($expectedSubraceClass, $subrace);
        self::assertSame($raceCode, $subrace->getRaceCode());
        self::assertSame($subraceCode, $subrace->getSubRaceCode());
    }

    public function provideSubraceCodesAndClass()
    {
        return [
            ['dwarf', 'common', CommonDwarf::class],
            ['dwarf', 'mountain', MountainDwarf::class],
            ['dwarf', 'wood', WoodDwarf::class],
            ['elf', 'common', CommonElf::class],
            ['elf', 'dark', DarkElf::class],
            ['elf', 'green', GreenElf::class],
            ['hobbit', 'common', CommonHobbit::class],
            ['human', 'common', CommonHuman::class],
            ['human', 'highlander', Highlander::class],
            ['kroll', 'common', CommonKroll::class],
            ['kroll', 'wild', WildKroll::class],
            ['orc', 'common', CommonOrc::class],
            ['orc', 'goblin', Goblin::class],
            ['orc', 'skurut', Skurut::class],
        ];
    }

    /**
     * @test
     */
    public function I_can_not_create_subrace_by_unknown_codes()
    {
        $this->expectException(\DrdPlus\Races\Exceptions\UnknownRaceCode::class);
        $this->expectExceptionMessageRegExp('~dragonius.+drunkalius~');
        RacesFactory::getSubRaceByCodes($this->createRaceCode('dragonius'), $this->createSubraceCode('drunkalius'));
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|RaceCode
     */
    private function createRaceCode($value)
    {
        $raceCode = $this->mockery(RaceCode::class);
        $raceCode->shouldReceive('getValue')
            ->andReturn($value);

        return $raceCode;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|SubRaceCode
     */
    private function createSubraceCode($value)
    {
        $subraceCode = $this->mockery(SubRaceCode::class);
        $subraceCode->shouldReceive('getValue')
            ->andReturn($value);

        return $subraceCode;
    }
}