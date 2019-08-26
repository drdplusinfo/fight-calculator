<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat;

use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Combat\BaseProperties;
use DrdPlus\Properties\Combat\Fight;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Tables\Body\CorrectionByHeightTable;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Properties\Combat\Partials\CharacteristicForGameTest;

class FightTest extends CharacteristicForGameTest
{
    /**
     * @test
     */
    public function I_can_get_property_easily()
    {
        $fight = Fight::getIt(
            $this->createProfessionCode(ProfessionCode::FIGHTER),
            $this->createBaseProperties(0),
            $height = $this->createHeight(0),
            $this->createTablesWithCorrectionByHeightTable($height, 123)
        );
        self::assertInstanceOf(Fight::class, $fight);
        self::assertSame(123, $fight->getValue());
    }

    /**
     * @return Fight
     */
    protected function createSut()
    {
        return Fight::getIt(
            ProfessionCode::getIt(ProfessionCode::FIGHTER),
            $this->createBaseProperties(0, 0, 0, 0),
            $height = $this->createHeight(4),
            $this->createTablesWithCorrectionByHeightTable($height, 0)
        );
    }

    /**
     * @param $agility
     * @param $knack
     * @param $intelligence
     * @param $charisma
     * @return BaseProperties|\Mockery\MockInterface
     */
    private function createBaseProperties($agility, $knack = 0, $intelligence = 0, $charisma = 0)
    {
        $properties = \Mockery::mock(BaseProperties::class);
        $properties->shouldReceive('getAgility')
            ->andReturn(Agility::getIt($agility));
        $properties->shouldReceive('getKnack')
            ->andReturn(Knack::getIt($knack));
        $properties->shouldReceive('getIntelligence')
            ->andReturn(Intelligence::getIt($intelligence));
        $properties->shouldReceive('getCharisma')
            ->andReturn(Charisma::getIt($charisma));

        return $properties;
    }

    /**
     * @param $value
     * @return Height|\Mockery\MockInterface
     */
    private function createHeight($value)
    {
        $height = \Mockery::mock(Height::class);
        $height->shouldReceive('getValue')
            ->andReturn($value);
        $height->shouldReceive('__toString')
            ->andReturn((string)$value);

        return $height;
    }

    /**
     * @param ProfessionCode $professionCode
     * @param BaseProperties $baseProperties
     * @param int $expectedFightNumber
     * @test
     * @dataProvider provideProfessionInfo
     */
    public function I_can_get_fight_number_for_every_profession(
        ProfessionCode $professionCode,
        BaseProperties $baseProperties,
        $expectedFightNumber
    )
    {
        $height = $this->createHeight(123);
        $correctionByHeightTable = $this->createTablesWithCorrectionByHeightTable($height, $correction = -5);
        $fightNumber = Fight::getIt($professionCode, $baseProperties, $height, $correctionByHeightTable);
        self::assertSame($expectedFightNumber + $correction, $fightNumber->getValue(), "Unexpected fight number for {$professionCode}");
        self::assertSame((string)($expectedFightNumber + $correction), (string)$fightNumber);
    }

    public function provideProfessionInfo(): array
    {
        return [
            [
                ProfessionCode::getIt(ProfessionCode::FIGHTER),
                $this->createBaseProperties($agility = 123, 0, 0, 0),
                $agility,
            ],
            [
                ProfessionCode::getIt(ProfessionCode::THIEF),
                $this->createBaseProperties($agility = 456, $knack = 567, 0, 0),
                (int)round(($agility + $knack) / 2),
            ],
            [
                ProfessionCode::getIt(ProfessionCode::RANGER),
                $this->createBaseProperties($agility = 123, $knack = 234, 0, 0),
                (int)round(($agility + $knack) / 2),
            ],
            [
                ProfessionCode::getIt(ProfessionCode::WIZARD),
                $this->createBaseProperties($agility = 456, 0, $intelligence = 567, 0),
                (int)round(($agility + $intelligence) / 2),
            ],
            [
                ProfessionCode::getIt(ProfessionCode::THEURGIST),
                $this->createBaseProperties($agility = 123, 0, $intelligence = 234, 0),
                (int)round(($agility + $intelligence) / 2),
            ],
            [
                ProfessionCode::getIt(ProfessionCode::PRIEST),
                $this->createBaseProperties($agility = 456, 0, 0, $charisma = 567),
                (int)round(($agility + $charisma) / 2),
            ],
            [
                ProfessionCode::getIt(ProfessionCode::COMMONER),
                $this->createBaseProperties($agility = 456, 0, 0, $charisma = 567),
                0, // whatever properties commoner has, its fight is still zero
            ],
        ];
    }

    /**
     * @param Height $height
     * @param int $correction
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithCorrectionByHeightTable(Height $height, $correction)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getCorrectionByHeightTable')
            ->andReturn($correctionByHeightTable = $this->mockery(CorrectionByHeightTable::class));
        $correctionByHeightTable->shouldReceive('getCorrectionByHeight')
            ->with($height)
            ->andReturn($correction);

        return $tables;
    }

    /**
     * @test
     */
    public function I_can_not_get_fight_for_unknown_profession()
    {
        $this->expectException(\DrdPlus\Properties\Combat\Exceptions\UnknownProfession::class);
        Fight::getIt(
            $this->createProfessionCode('monk'),
            $this->createBaseProperties(0, 0, 0, 0),
            $height = $this->createHeight(0),
            $this->createTablesWithCorrectionByHeightTable($height, 0)
        );
    }

    /**
     * @param $value
     * @return ProfessionCode|\Mockery\MockInterface
     */
    private function createProfessionCode($value)
    {
        $professionCode = $this->mockery(ProfessionCode::class);
        $professionCode->shouldReceive('getValue')
            ->andReturn($value);
        $professionCode->shouldReceive('__toString')
            ->andReturn((string)$value);

        return $professionCode;
    }
}