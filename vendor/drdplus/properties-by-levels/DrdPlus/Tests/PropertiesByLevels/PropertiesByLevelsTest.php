<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\PropertiesByLevels;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Combat\Attack;
use DrdPlus\Properties\Combat\Defense;
use DrdPlus\Properties\Combat\Fight;
use DrdPlus\Properties\Combat\Shooting;
use DrdPlus\PropertiesByFate\PropertiesByFate;
use DrdPlus\PropertiesByLevels\FirstLevelProperties;
use DrdPlus\PropertiesByLevels\NextLevelsProperties;
use DrdPlus\PropertiesByLevels\PropertiesByLevels;
use DrdPlus\Professions\Profession;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Body\Age;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Properties\Body\BodyWeightInKg;
use DrdPlus\Properties\Derived\Beauty;
use DrdPlus\Properties\Derived\Dangerousness;
use DrdPlus\Properties\Derived\Dignity;
use DrdPlus\Properties\Derived\Endurance;
use DrdPlus\Properties\Derived\FatigueBoundary;
use DrdPlus\Properties\Derived\Senses;
use DrdPlus\Properties\Derived\Speed;
use DrdPlus\Properties\Derived\Toughness;
use DrdPlus\Properties\Derived\WoundBoundary;
use DrdPlus\Races\Humans\CommonHuman;
use DrdPlus\Races\Race;
use DrdPlus\Tables\Body\CorrectionByHeightTable;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class PropertiesByLevelsTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider getCombination
     * @param Race $race
     * @param GenderCode $genderCode
     * @param PropertiesByFate $propertiesByFate
     * @param ProfessionLevels $professionLevels
     * @param Tables $tables
     * @param BodyWeightInKg $weightInKgAdjustment
     * @param HeightInCm $heightInCmAdjustment
     * @param Age $age
     * @param int $expectedStrength
     * @param int $expectedAgility
     * @param int $expectedKnack
     * @param int $expectedWill
     * @param int $expectedIntelligence
     * @param int $expectedCharisma
     * @param int $expectedFight
     */
    public function I_can_create_properties_for_any_combination(
        Race $race,
        GenderCode $genderCode,
        PropertiesByFate $propertiesByFate,
        ProfessionLevels $professionLevels,
        Tables $tables,
        BodyWeightInKg $weightInKgAdjustment,
        HeightInCm $heightInCmAdjustment,
        Age $age,
        int $expectedStrength,
        int $expectedAgility,
        int $expectedKnack,
        int $expectedWill,
        int $expectedIntelligence,
        int $expectedCharisma,
        int $expectedFight
    )
    {
        $properties = new PropertiesByLevels(
            $race,
            $genderCode,
            $propertiesByFate,
            $professionLevels,
            $weightInKgAdjustment,
            $heightInCmAdjustment,
            $age,
            $tables
        );

        self::assertInstanceOf(FirstLevelProperties::class, $properties->getFirstLevelProperties());
        self::assertInstanceOf(NextLevelsProperties::class, $properties->getNextLevelsProperties());

        self::assertSame($expectedStrength, $properties->getStrength()->getValue(), "$race $genderCode");
        self::assertSame($expectedAgility, $properties->getAgility()->getValue(), "$race $genderCode");
        self::assertSame($expectedKnack, $properties->getKnack()->getValue(), "$race $genderCode");
        self::assertSame($expectedWill, $properties->getWill()->getValue(), "$race $genderCode");
        self::assertSame($expectedIntelligence, $properties->getIntelligence()->getValue(), "$race $genderCode");
        self::assertSame($expectedCharisma, $properties->getCharisma()->getValue(), "$race $genderCode");

        self::assertSame($weightInKgAdjustment, $properties->getBodyWeightInKgAdjustment());
        self::assertGreaterThan($weightInKgAdjustment->getValue(), $properties->getWeightInKg()->getValue(), "$race $genderCode");
        self::assertSame($heightInCmAdjustment, $properties->getHeightInCmAdjustment());
        self::assertGreaterThan($heightInCmAdjustment->getValue(), $properties->getHeightInCm()->getValue(), "$race $genderCode");
        self::assertEquals($expectedHeight = Height::getIt($properties->getHeightInCm(), $tables), $properties->getHeight());
        self::assertSame($age, $properties->getAge());
        $expectedToughness = Toughness::getIt(Strength::getIt($expectedStrength), $race->getRaceCode(), $race->getSubraceCode(), $tables);
        self::assertInstanceOf(Toughness::class, $properties->getToughness());
        self::assertSame($expectedToughness->getValue(), $properties->getToughness()->getValue(), "$race $genderCode");
        $expectedEndurance = Endurance::getIt(Strength::getIt($expectedStrength), Will::getIt($expectedWill));
        self::assertInstanceOf(Endurance::class, $properties->getEndurance());
        self::assertSame($expectedEndurance->getValue(), $properties->getEndurance()->getValue(), "$race $genderCode");
        $expectedSize = Size::getIt($race->getSize($genderCode, $tables) + 1); /* size bonus by strength */
        self::assertInstanceOf(Size::class, $properties->getSize(), "$race $genderCode");
        self::assertSame($expectedSize->getValue(), $properties->getSize()->getValue(), "$race $genderCode");
        $expectedSpeed = Speed::getIt(Strength::getIt($expectedStrength), Agility::getIt($expectedAgility), $expectedHeight);
        self::assertInstanceOf(Speed::class, $properties->getSpeed(), "$race $genderCode");
        self::assertSame($expectedSpeed->getValue(), $properties->getSpeed()->getValue(), "$race $genderCode");
        $expectedSenses = Senses::getIt(
            Knack::getIt($expectedKnack),
            RaceCode::getIt($race->getRaceCode()),
            SubRaceCode::getIt($race->getSubraceCode()),
            $tables
        );
        self::assertInstanceOf(Senses::class, $properties->getSenses());
        self::assertSame($expectedSenses->getValue(), $properties->getSenses()->getValue(), "$race $genderCode");
        $expectedBeauty = Beauty::getIt(Agility::getIt($expectedAgility), Knack::getIt($expectedKnack), Charisma::getIt($expectedCharisma));
        self::assertInstanceOf(Beauty::class, $properties->getBeauty());
        self::assertSame($expectedBeauty->getValue(), $properties->getBeauty()->getValue(), "$race $genderCode");
        $expectedDangerousness = Dangerousness::getIt(Strength::getIt($expectedStrength), Will::getIt($expectedWill), Charisma::getIt($expectedCharisma));
        self::assertInstanceOf(Dangerousness::class, $properties->getDangerousness());
        self::assertSame($expectedDangerousness->getValue(), $properties->getDangerousness()->getValue(), "$race $genderCode");
        $expectedDignity = Dignity::getIt(Intelligence::getIt($expectedIntelligence), Will::getIt($expectedWill), Charisma::getIt($expectedCharisma));
        self::assertInstanceOf(Dignity::class, $properties->getDignity());
        self::assertSame($expectedDignity->getValue(), $properties->getDignity()->getValue(), "$race $genderCode");

        self::assertInstanceOf(Fight::class, $properties->getFight());
        self::assertSame($expectedFight, $properties->getFight()->getValue(), "$race $genderCode with height $expectedHeight");
        $expectedAttack = Attack::getIt(Agility::getIt($expectedAgility));
        self::assertInstanceOf(Attack::class, $properties->getAttack());
        self::assertSame($expectedAttack->getValue(), $properties->getAttack()->getValue(), "$race $genderCode");
        $expectedShooting = Shooting::getIt(Knack::getIt($expectedKnack));
        self::assertInstanceOf(Shooting::class, $properties->getShooting());
        self::assertSame($expectedShooting->getValue(), $properties->getShooting()->getValue(), "$race $genderCode");
        $expectedDefense = Defense::getIt(Agility::getIt($expectedAgility));
        self::assertInstanceOf(Defense::class, $properties->getDefense());
        self::assertSame($expectedDefense->getValue(), $properties->getDefense()->getValue(), "$race $genderCode");

        $expectedWoundBoundary = WoundBoundary::getIt($expectedToughness, $tables);
        self::assertInstanceOf(WoundBoundary::class, $properties->getWoundBoundary());
        self::assertSame($expectedWoundBoundary->getValue(), $properties->getWoundBoundary()->getValue());
        $expectedFatigueBoundary = FatigueBoundary::getIt($expectedEndurance, $tables);
        self::assertInstanceOf(FatigueBoundary::class, $properties->getFatigueBoundary());
        self::assertSame($expectedFatigueBoundary->getValue(), $properties->getFatigueBoundary()->getValue());
    }

    public function getCombination()
    {
        $male = GenderCode::getIt(GenderCode::MALE);
        $female = GenderCode::getIt(GenderCode::FEMALE);
        $propertiesByFate = $this->createPropertiesByFate();
        $professionLevels = $this->createProfessionLevels();
        $heightInCm = HeightInCm::getIt(123.4);
        $tables = $this->createTables($correctionFromHeight = 369);
        $weightInKgAdjustment = BodyWeightInKg::getIt(0.001);
        $age = Age::getIt(15);
        $baseOfExpectedStrength = $professionLevels->getNextLevelsStrengthModifier() + 3; /* default max strength increment */
        $baseOfExpectedAgility = $professionLevels->getNextLevelsAgilityModifier() + 3; /* default max agility increment */
        $baseOfExpectedKnack = $professionLevels->getNextLevelsKnackModifier() + 3; /* default max knack increment */
        $baseOfExpectedWill = $professionLevels->getNextLevelsWillModifier() + 3; /* default max knack increment */
        $baseOfExpectedIntelligence = $professionLevels->getNextLevelsIntelligenceModifier() + 3; /* default max knack increment */
        $baseOfExpectedCharisma = $professionLevels->getNextLevelsCharismaModifier() + 3; /* default max charisma increment */
        $expectedFight = $baseOfExpectedAgility /* fighter */ + $correctionFromHeight;

        return [
            [
                CommonHuman::getIt(), $male, $propertiesByFate, $professionLevels, $tables,
                $weightInKgAdjustment, $heightInCm, $age, $baseOfExpectedStrength, $baseOfExpectedAgility, $baseOfExpectedKnack,
                $baseOfExpectedWill, $baseOfExpectedIntelligence, $baseOfExpectedCharisma, $expectedFight,
            ],
            [
                CommonHuman::getIt(), $female, $propertiesByFate, $professionLevels, $tables, $weightInKgAdjustment,
                $heightInCm, $age,
                $baseOfExpectedStrength - 1 /* human female */, $baseOfExpectedAgility, $baseOfExpectedKnack,
                $baseOfExpectedWill, $baseOfExpectedIntelligence, $baseOfExpectedCharisma + 1 /* human female */,
                $expectedFight,
            ],
            // ... no reason to check every race
        ];
    }

    /**
     * @param int $correctionByHeight
     * @return \Mockery\MockInterface|Tables
     */
    private function createTables($correctionByHeight)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('__toString')
            ->andReturn('We are mocked tables so note it for your description');
        $tables->shouldReceive('getCorrectionByHeightTable')
            ->andReturn($correctionByHeightTable = $this->mockery(CorrectionByHeightTable::class));
        $correctionByHeightTable->shouldReceive('getCorrectionByHeight')
            ->with($this->type(Height::class))
            ->andReturn($correctionByHeight);
        $tables->shouldDeferMissing();

        return $tables;
    }

    /**
     * @return PropertiesByFate|\Mockery\MockInterface
     */
    private function createPropertiesByFate()
    {
        $propertiesByFate = \Mockery::mock(PropertiesByFate::class);
        $propertiesByFate->shouldReceive('getProperty')
            ->with(PropertyCode::getIt(PropertyCode::STRENGTH))
            ->andReturn($strength = Strength::getIt(123));
        $propertiesByFate->shouldReceive('getStrength')
            ->andReturn($strength);
        $propertiesByFate->shouldReceive('getProperty')
            ->with(PropertyCode::getIt(PropertyCode::AGILITY))
            ->andReturn($agility = Agility::getIt(234));
        $propertiesByFate->shouldReceive('getAgility')
            ->andReturn($agility);
        $propertiesByFate->shouldReceive('getProperty')
            ->with(PropertyCode::getIt(PropertyCode::KNACK))
            ->andReturn($knack = Knack::getIt(345));
        $propertiesByFate->shouldReceive('getKnack')
            ->andReturn($knack);
        $propertiesByFate->shouldReceive('getProperty')
            ->with(PropertyCode::getIt(PropertyCode::WILL))
            ->andReturn($will = Will::getIt(456));
        $propertiesByFate->shouldReceive('getWill')
            ->andReturn($will);
        $propertiesByFate->shouldReceive('getProperty')
            ->with(PropertyCode::getIt(PropertyCode::INTELLIGENCE))
            ->andReturn($intelligence = Intelligence::getIt(567));
        $propertiesByFate->shouldReceive('getIntelligence')
            ->andReturn($intelligence);
        $propertiesByFate->shouldReceive('getProperty')
            ->with(PropertyCode::getIt(PropertyCode::CHARISMA))
            ->andReturn($charisma = Charisma::getIt(678));
        $propertiesByFate->shouldReceive('getCharisma')
            ->andReturn($charisma);

        return $propertiesByFate;
    }

    /**
     * @return ProfessionLevels|\Mockery\MockInterface
     */
    private function createProfessionLevels()
    {
        $professionLevels = \Mockery::mock(ProfessionLevels::class);
        $professionLevels->shouldReceive('getFirstLevelPropertyModifier')
            ->with(PropertyCode::STRENGTH)
            ->andReturn($strength = 1234);
        $professionLevels->shouldReceive('getFirstLevelStrengthModifier')
            ->andReturn($strength);
        $professionLevels->shouldReceive('getFirstLevelPropertyModifier')
            ->with(PropertyCode::AGILITY)
            ->andReturn($agility = 2345);
        $professionLevels->shouldReceive('getFirstLevelAgilityModifier')
            ->andReturn($agility);
        $professionLevels->shouldReceive('getFirstLevelPropertyModifier')
            ->with(PropertyCode::KNACK)
            ->andReturn($knack = 3456);
        $professionLevels->shouldReceive('getFirstLevelKnackModifier')
            ->andReturn($knack);
        $professionLevels->shouldReceive('getFirstLevelPropertyModifier')
            ->with(PropertyCode::WILL)
            ->andReturn($will = 3456);
        $professionLevels->shouldReceive('getFirstLevelWillModifier')
            ->andReturn($will);
        $professionLevels->shouldReceive('getFirstLevelPropertyModifier')
            ->with(PropertyCode::INTELLIGENCE)
            ->andReturn($intelligence = 5678);
        $professionLevels->shouldReceive('getFirstLevelIntelligenceModifier')
            ->andReturn($intelligence);
        $professionLevels->shouldReceive('getFirstLevelPropertyModifier')
            ->with(PropertyCode::CHARISMA)
            ->andReturn($charisma = 6789);
        $professionLevels->shouldReceive('getFirstLevelPropertyModifier')
            ->andReturn($charisma);

        $professionLevels->shouldReceive('getNextLevelsStrengthModifier')
            ->andReturn(2); // is not limited by FirstLevelProperties and has to fit to wounds table range
        $professionLevels->shouldReceive('getNextLevelsAgilityModifier')
            ->andReturn(23456);
        $professionLevels->shouldReceive('getNextLevelsKnackModifier')
            ->andReturn(34567);
        $professionLevels->shouldReceive('getNextLevelsWillModifier')
            ->andReturn(4); // is not limited by FirstLevelProperties and has to fit to wounds table range
        $professionLevels->shouldReceive('getNextLevelsIntelligenceModifier')
            ->andReturn(56789);
        $professionLevels->shouldReceive('getNextLevelsCharismaModifier')
            ->andReturn(67890);

        $professionLevels->shouldReceive('getFirstLevel')
            ->andReturn($firstLevel = \Mockery::mock(ProfessionFirstLevel::class));
        $firstLevel->shouldReceive('getProfession')
            ->andReturn($profession = \Mockery::mock(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn(ProfessionCode::FIGHTER);
        $profession->shouldReceive('__toString')
            ->andReturn(ProfessionCode::FIGHTER);
        $profession->shouldReceive('getCode')
            ->andReturn(ProfessionCode::getIt(ProfessionCode::FIGHTER));

        return $professionLevels;
    }
}