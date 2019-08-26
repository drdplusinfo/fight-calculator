<?php declare(strict_types=1);

namespace DrdPlus\Tests\PropertiesByFate;

use DrdPlus\Codes\History\ChoiceCode;
use DrdPlus\Codes\History\FateCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\PropertiesByFate\PropertiesByFate;
use DrdPlus\PropertiesByFate\FortuneProperties;
use DrdPlus\BaseProperties\BasePropertiesFactory;
use DrdPlus\Tables\History\InfluenceOfFortuneTable;
use DrdPlus\Tables\Tables;
use Granam\DiceRolls\Templates\Rolls\Roll1d6;

class FortunePropertiesTest extends PropertiesByFateTest
{
    /**
     * @test
     */
    public function I_can_get_every_dice_roll()
    {
        $fortuneProperties = new FortuneProperties(
            $this->createRoll($strengthRoll = 1),
            $this->createRoll($agilityRoll = 2),
            $this->createRoll($knackRoll = 3),
            $this->createRoll($willRoll = 4),
            $this->createRoll($intelligenceRoll = 5),
            $this->createRoll($charismaRoll = 6),
            $fateCode = FateCode::getIt(FateCode::COMBINATION_OF_PROPERTIES_AND_BACKGROUND),
            $this->createProfession([]), // all properties as secondary
            $this->createTablesWithInfluenceOfFortuneTable($testMultiplier = 456, false /* for secondary properties */),
            new BasePropertiesFactory()
        );
        $this->I_get_expected_choice_code($fortuneProperties);
        $this->I_get_fate_code_created_with($fortuneProperties, $fateCode);
        $this->I_can_get_property_by_its_code($fortuneProperties);
        self::assertSame($strengthRoll, $fortuneProperties->getStrengthRoll());
        self::assertSame($agilityRoll, $fortuneProperties->getAgilityRoll());
        self::assertSame($knackRoll, $fortuneProperties->getKnackRoll());
        self::assertSame($willRoll, $fortuneProperties->getWillRoll());
        self::assertSame($intelligenceRoll, $fortuneProperties->getIntelligenceRoll());
        self::assertSame($charismaRoll, $fortuneProperties->getCharismaRoll());
    }

    protected function I_get_expected_choice_code(PropertiesByFate $fortuneProperties)
    {
        self::assertSame(ChoiceCode::getIt(ChoiceCode::FORTUNE), $fortuneProperties->getChoiceCode());
    }

    protected function I_get_fate_code_created_with(PropertiesByFate $fortuneProperties, FateCode $expectedFateCode)
    {
        self::assertSame($expectedFateCode, $fortuneProperties->getFateCode());
    }

    /**
     * @test
     * @dataProvider providePropertyRolls
     * @param int $strengthRoll
     * @param int $agilityRoll
     * @param int $knackRoll
     * @param int $willRoll
     * @param int $intelligenceRoll
     * @param int $charismaRoll
     */
    public function I_can_get_fortune_properties_tested_as_primary(
        int $strengthRoll,
        int $agilityRoll,
        int $knackRoll,
        int $willRoll,
        int $intelligenceRoll,
        int $charismaRoll
    )
    {
        $fortuneProperties = new FortuneProperties(
            $this->createRoll($strengthRoll),
            $this->createRoll($agilityRoll),
            $this->createRoll($knackRoll),
            $this->createRoll($willRoll),
            $this->createRoll($intelligenceRoll),
            $this->createRoll($charismaRoll),
            FateCode::getIt(FateCode::EXCEPTIONAL_PROPERTIES),
            $this->createProfession([
                PropertyCode::STRENGTH,
                PropertyCode::AGILITY,
                PropertyCode::KNACK,
                PropertyCode::WILL,
                PropertyCode::INTELLIGENCE,
                PropertyCode::CHARISMA,
            ]),
            $this->createTablesWithInfluenceOfFortuneTable($testMultiplier = 456, true /* for primary properties */),
            new BasePropertiesFactory()
        );
        self::assertSame($strengthRoll * $testMultiplier, $fortuneProperties->getStrength()->getValue());
        self::assertSame($strengthRoll, $fortuneProperties->getStrengthRoll());
        self::assertSame($agilityRoll * $testMultiplier, $fortuneProperties->getAgility()->getValue());
        self::assertSame($agilityRoll, $fortuneProperties->getAgilityRoll());
        self::assertSame($knackRoll * $testMultiplier, $fortuneProperties->getKnack()->getValue());
        self::assertSame($knackRoll, $fortuneProperties->getKnackRoll());
        self::assertSame($willRoll * $testMultiplier, $fortuneProperties->getWill()->getValue());
        self::assertSame($willRoll, $fortuneProperties->getWillRoll());
        self::assertSame($intelligenceRoll * $testMultiplier, $fortuneProperties->getIntelligence()->getValue());
        self::assertSame($intelligenceRoll, $fortuneProperties->getIntelligenceRoll());
        self::assertSame($charismaRoll * $testMultiplier, $fortuneProperties->getCharisma()->getValue());
        self::assertSame($charismaRoll, $fortuneProperties->getCharismaRoll());
    }

    public function providePropertyRolls(): array
    {
        return [
            [1, 2, 3, 4, 5, 6],
        ];
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|Roll1d6
     */
    private function createRoll($value)
    {
        $roll = $this->mockery(Roll1d6::class);
        $roll->shouldReceive('getValue')
            ->andReturn($value);

        return $roll;
    }

    /**
     * @param int $testMultiplier
     * @param bool $forPrimaryProperty
     * @return Tables|\Mockery\MockInterface
     */
    private function createTablesWithInfluenceOfFortuneTable($testMultiplier, $forPrimaryProperty)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getInfluenceOfFortuneTable')
            ->andReturn($influenceOfFortuneTable = $this->mockery(InfluenceOfFortuneTable::class));
        $influenceOfFortuneTable->shouldReceive($forPrimaryProperty
            ? 'getPrimaryPropertyOnFate'
            : 'getSecondaryPropertyOnFate'
        )
            ->with($this->type(FateCode::class), $this->type(Roll1d6::class))
            ->andReturnUsing(function (FateCode $fateCode, Roll1d6 $roll) use ($testMultiplier) {
                return $roll->getValue() * $testMultiplier;
            });

        return $tables;
    }

    /**
     * @test
     * @dataProvider providePropertyRolls
     * @param int $strengthRoll
     * @param int $agilityRoll
     * @param int $knackRoll
     * @param int $willRoll
     * @param int $intelligenceRoll
     * @param int $charismaRoll
     */
    public function I_can_get_fortune_properties_tested_as_secondary(
        int $strengthRoll,
        int $agilityRoll,
        int $knackRoll,
        int $willRoll,
        int $intelligenceRoll,
        int $charismaRoll
    )
    {
        $fortuneProperties = new FortuneProperties(
            $this->createRoll($strengthRoll),
            $this->createRoll($agilityRoll),
            $this->createRoll($knackRoll),
            $this->createRoll($willRoll),
            $this->createRoll($intelligenceRoll),
            $this->createRoll($charismaRoll),
            FateCode::getIt(FateCode::COMBINATION_OF_PROPERTIES_AND_BACKGROUND),
            $this->createProfession([]), // all properties as secondary
            $this->createTablesWithInfluenceOfFortuneTable($testMultiplier = 456, false /* for secondary properties */),
            new BasePropertiesFactory()
        );
        self::assertSame($strengthRoll * $testMultiplier, $fortuneProperties->getStrength()->getValue());
        self::assertSame($strengthRoll, $fortuneProperties->getStrengthRoll());
        self::assertSame($agilityRoll * $testMultiplier, $fortuneProperties->getAgility()->getValue());
        self::assertSame($agilityRoll, $fortuneProperties->getAgilityRoll());
        self::assertSame($knackRoll * $testMultiplier, $fortuneProperties->getKnack()->getValue());
        self::assertSame($knackRoll, $fortuneProperties->getKnackRoll());
        self::assertSame($willRoll * $testMultiplier, $fortuneProperties->getWill()->getValue());
        self::assertSame($willRoll, $fortuneProperties->getWillRoll());
        self::assertSame($intelligenceRoll * $testMultiplier, $fortuneProperties->getIntelligence()->getValue());
        self::assertSame($intelligenceRoll, $fortuneProperties->getIntelligenceRoll());
        self::assertSame($charismaRoll * $testMultiplier, $fortuneProperties->getCharisma()->getValue());
        self::assertSame($charismaRoll, $fortuneProperties->getCharismaRoll());
    }
}