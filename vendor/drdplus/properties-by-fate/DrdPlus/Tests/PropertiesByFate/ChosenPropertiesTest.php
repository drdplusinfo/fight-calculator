<?php
declare(strict_types=1);

namespace DrdPlus\Tests\PropertiesByFate;

use DrdPlus\Codes\History\ChoiceCode;
use DrdPlus\Codes\History\FateCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\PropertiesByFate\ChosenProperties;
use DrdPlus\PropertiesByFate\PropertiesByFate;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Tables\History\PlayerDecisionsTable;
use DrdPlus\Tables\Tables;

class ChosenPropertiesTest extends PropertiesByFateTest
{
    /**
     * @test
     * @dataProvider provideChosenProperties
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_get_chosen_properties_tested_as_primary(
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence, int $charisma
    ): void
    {
        $chosenProperties = new ChosenProperties(
            Strength::getIt($strength),
            Agility::getIt($agility),
            Knack::getIt($knack),
            Will::getIt($will),
            Intelligence::getIt($intelligence),
            Charisma::getIt($charisma),
            $fateCode = FateCode::getIt(FateCode::COMBINATION_OF_PROPERTIES_AND_BACKGROUND),
            $this->createProfession([
                PropertyCode::STRENGTH,
                PropertyCode::AGILITY,
                PropertyCode::KNACK,
                PropertyCode::WILL,
                PropertyCode::INTELLIGENCE,
                PropertyCode::CHARISMA,
            ]),
            $this->createTablesWithPlayerDecisionsTable(
                $strength + $agility + $knack + $will + $intelligence + $charisma,
                0,
                max($strength, $agility, $knack, $will, $intelligence, $charisma)
            )
        );
        $this->I_get_expected_choice_code($chosenProperties);
        $this->I_get_fate_code_created_with($chosenProperties, $fateCode);
        $this->I_can_get_property_by_its_code($chosenProperties);
        self::assertSame($strength, $chosenProperties->getStrength()->getValue());
        self::assertSame($agility, $chosenProperties->getAgility()->getValue());
        self::assertSame($knack, $chosenProperties->getKnack()->getValue());
        self::assertSame($will, $chosenProperties->getWill()->getValue());
        self::assertSame($intelligence, $chosenProperties->getIntelligence()->getValue());
        self::assertSame($charisma, $chosenProperties->getCharisma()->getValue());
    }

    public function provideChosenProperties(): array
    {
        return [
            [1, 2, 3, 4, 5, 6],
        ];
    }

    /**
     * @param int $primaryPropertiesBonus
     * @param int $secondaryPropertiesBonus
     * @param int $upToSingleProperty
     * @return Tables|\Mockery\MockInterface
     */
    private function createTablesWithPlayerDecisionsTable(int $primaryPropertiesBonus, int $secondaryPropertiesBonus, int $upToSingleProperty)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getPlayerDecisionsTable')
            ->andReturn($playerDecisionsTable = $this->mockery(PlayerDecisionsTable::class));
        $playerDecisionsTable->shouldReceive('getPointsToSecondaryProperties')
            ->andReturn($secondaryPropertiesBonus);
        $playerDecisionsTable->shouldReceive('getPointsToPrimaryProperties')
            ->andReturn($primaryPropertiesBonus);
        $playerDecisionsTable->shouldReceive('getMaximumToSingleProperty')
            ->andReturn($upToSingleProperty);

        return $tables;
    }

    protected function I_get_expected_choice_code(PropertiesByFate $chosenProperties)
    {
        self::assertSame(ChoiceCode::getIt(ChoiceCode::PLAYER_DECISION), $chosenProperties->getChoiceCode());
    }

    protected function I_get_fate_code_created_with(
        PropertiesByFate $chosenProperties,
        FateCode $expectedFateCode
    )
    {
        self::assertSame($expectedFateCode, $chosenProperties->getFateCode());
    }

    /**
     * @test
     * @dataProvider provideChosenProperties
     * @expectedException \DrdPlus\PropertiesByFate\Exceptions\InvalidValueOfChosenProperty
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_not_use_higher_than_allowed_chosen_property_tested_as_primary(
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence,
        int $charisma
    )
    {
        new ChosenProperties(
            Strength::getIt($strength),
            Agility::getIt($agility),
            Knack::getIt($knack),
            Will::getIt($will),
            Intelligence::getIt($intelligence),
            Charisma::getIt($charisma),
            FateCode::getIt(FateCode::GOOD_BACKGROUND),
            $this->createProfession([
                PropertyCode::STRENGTH,
                PropertyCode::AGILITY,
                PropertyCode::KNACK,
                PropertyCode::WILL,
                PropertyCode::INTELLIGENCE,
                PropertyCode::CHARISMA,
            ]),
            $this->createTablesWithPlayerDecisionsTable(
                $strength + $agility + $knack + $will + $intelligence + $charisma,
                0,
                max($strength, $agility, $knack, $will, $intelligence, $charisma) - 1 /* allowed a little bit lesser than given */
            )
        );
    }

    /**
     * @test
     * @dataProvider provideChosenProperties
     * @expectedException \DrdPlus\PropertiesByFate\Exceptions\InvalidSumOfChosenProperties
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_not_use_higher_than_expected_chosen_properties_sum_tested_as_primary(
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence,
        int $charisma
    )
    {
        new ChosenProperties(
            Strength::getIt($strength),
            Agility::getIt($agility),
            Knack::getIt($knack),
            Will::getIt($will),
            Intelligence::getIt($intelligence),
            Charisma::getIt($charisma),
            FateCode::getIt(FateCode::EXCEPTIONAL_PROPERTIES),
            $this->createProfession([
                PropertyCode::STRENGTH,
                PropertyCode::AGILITY,
                PropertyCode::KNACK,
                PropertyCode::WILL,
                PropertyCode::INTELLIGENCE,
                PropertyCode::CHARISMA,
            ]),
            $this->createTablesWithPlayerDecisionsTable(
                $strength + $agility + $knack + $will + $intelligence + $charisma - 1 /* allowed a little bit less than given*/,
                0,
                max($strength, $agility, $knack, $will, $intelligence, $charisma)
            )
        );
    }

    /**
     * @test
     * @dataProvider provideChosenProperties
     * @expectedException \DrdPlus\PropertiesByFate\Exceptions\InvalidSumOfChosenProperties
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_not_use_lesser_than_expected_chosen_properties_sum_tested_as_primary(
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence,
        int $charisma
    )
    {
        new ChosenProperties(
            Strength::getIt($strength),
            Agility::getIt($agility),
            Knack::getIt($knack),
            Will::getIt($will),
            Intelligence::getIt($intelligence),
            Charisma::getIt($charisma),
            FateCode::getIt(FateCode::EXCEPTIONAL_PROPERTIES),
            $this->createProfession([
                PropertyCode::STRENGTH,
                PropertyCode::AGILITY,
                PropertyCode::KNACK,
                PropertyCode::WILL,
                PropertyCode::INTELLIGENCE,
                PropertyCode::CHARISMA,
            ]),
            $this->createTablesWithPlayerDecisionsTable(
                $strength + $agility + $knack + $will + $intelligence + $charisma + 1/* expected a little bit more than given*/,
                0,
                max($strength, $agility, $knack, $will, $intelligence, $charisma)
            )
        );
    }

    /**
     * @test
     * @dataProvider provideChosenProperties
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_get_chosen_properties_tested_as_secondary(
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence,
        int $charisma
    )
    {
        $chosenProperties = new ChosenProperties(
            Strength::getIt($strength),
            Agility::getIt($agility),
            Knack::getIt($knack),
            Will::getIt($will),
            Intelligence::getIt($intelligence),
            Charisma::getIt($charisma),
            FateCode::getIt(FateCode::COMBINATION_OF_PROPERTIES_AND_BACKGROUND),
            $this->createProfession([]), // all properties as secondary
            $this->createTablesWithPlayerDecisionsTable(
                0,
                $strength + $agility + $knack + $will + $intelligence + $charisma,
                max($strength, $agility, $knack, $will, $intelligence, $charisma)
            )
        );
        self::assertSame($strength, $chosenProperties->getStrength()->getValue());
        self::assertSame($agility, $chosenProperties->getAgility()->getValue());
        self::assertSame($knack, $chosenProperties->getKnack()->getValue());
        self::assertSame($will, $chosenProperties->getWill()->getValue());
        self::assertSame($intelligence, $chosenProperties->getIntelligence()->getValue());
        self::assertSame($charisma, $chosenProperties->getCharisma()->getValue());
    }

    /**
     * @test
     * @dataProvider provideChosenProperties
     * @expectedException \DrdPlus\PropertiesByFate\Exceptions\InvalidSumOfChosenProperties
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_not_use_higher_than_expected_chosen_properties_sum_tested_as_secondary(
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence,
        int $charisma
    )
    {
        new ChosenProperties(
            Strength::getIt($strength),
            Agility::getIt($agility),
            Knack::getIt($knack),
            Will::getIt($will),
            Intelligence::getIt($intelligence),
            Charisma::getIt($charisma),
            FateCode::getIt(FateCode::EXCEPTIONAL_PROPERTIES),
            $this->createProfession([]),
            $this->createTablesWithPlayerDecisionsTable(
                0,
                $strength + $agility + $knack + $will + $intelligence + $charisma - 1 /* allowed a little bit less than given*/,
                max($strength, $agility, $knack, $will, $intelligence, $charisma)
            )
        );
    }

    /**
     * @test
     * @dataProvider provideChosenProperties
     * @expectedException \DrdPlus\PropertiesByFate\Exceptions\InvalidValueOfChosenProperty
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_not_use_higher_than_allowed_chosen_property_tested_as_secondary(
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence,
        int $charisma
    )
    {
        new ChosenProperties(
            Strength::getIt($strength),
            Agility::getIt($agility),
            Knack::getIt($knack),
            Will::getIt($will),
            Intelligence::getIt($intelligence),
            Charisma::getIt($charisma),
            FateCode::getIt(FateCode::GOOD_BACKGROUND),
            $this->createProfession([]), // no primary property
            $this->createTablesWithPlayerDecisionsTable(
                0,
                $strength + $agility + $knack + $will + $intelligence + $charisma,
                max($strength, $agility, $knack, $will, $intelligence, $charisma) - 1 /* allowed a little bit lesser than given */
            )
        );
    }

    /**
     * @test
     * @dataProvider provideChosenProperties
     * @expectedException \DrdPlus\PropertiesByFate\Exceptions\InvalidSumOfChosenProperties
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_not_use_lesser_than_expected_chosen_properties_sum_tested_as_secondary(
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence,
        int $charisma
    )
    {
        new ChosenProperties(
            Strength::getIt($strength),
            Agility::getIt($agility),
            Knack::getIt($knack),
            Will::getIt($will),
            Intelligence::getIt($intelligence),
            Charisma::getIt($charisma),
            FateCode::getIt(FateCode::COMBINATION_OF_PROPERTIES_AND_BACKGROUND),
            $this->createProfession([]), // no primary property
            $this->createTablesWithPlayerDecisionsTable(
                0,
                $strength + $agility + $knack + $will + $intelligence + $charisma + 1 /* expected a little bit more than given*/,
                max($strength, $agility, $knack, $will, $intelligence, $charisma)
            )
        );
    }

}