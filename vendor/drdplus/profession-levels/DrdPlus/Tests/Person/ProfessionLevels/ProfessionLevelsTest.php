<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Person\ProfessionLevels\Exceptions\MultiProfessionsAreProhibited;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionNextLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use \DrdPlus\Professions\Fighter;
use \DrdPlus\Professions\Priest;
use \DrdPlus\Professions\Profession;
use \DrdPlus\Professions\Ranger;
use \DrdPlus\Professions\Theurgist;
use \DrdPlus\Professions\Thief;
use \DrdPlus\Professions\Wizard;
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class ProfessionLevelsTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_create_it()
    {
        $zeroLevel = $this->createZeroLevel();
        $firstLevel = $this->createFirstLevel('fighter');
        $withFirstLevelOnly = new ProfessionLevels($zeroLevel, $firstLevel);
        self::assertNotNull($withFirstLevelOnly);

        $anotherInstance = ProfessionLevels::createIt($zeroLevel, $firstLevel);
        self::assertEquals($withFirstLevelOnly, $anotherInstance);

        $yetAnotherInstance = ProfessionLevels::createIt($zeroLevel, $firstLevel);
        self::assertNotSame($anotherInstance, $yetAnotherInstance);

        $withExplicitlyEmptyNextLevels = ProfessionLevels::createIt($zeroLevel, $firstLevel);
        self::assertEquals($withFirstLevelOnly, $withExplicitlyEmptyNextLevels);

        $withNextLevels = ProfessionLevels::createIt(
            $zeroLevel,
            $firstLevel,
            $nextLevels = [
                ProfessionNextLevel::createNextLevel(
                    Fighter::getIt(),
                    LevelRank::getIt(2),
                    Strength::getIt(1),
                    Agility::getIt(0),
                    Knack::getIt(0),
                    Will::getIt(0),
                    Intelligence::getIt(1),
                    Charisma::getIt(0)
                ),
            ]
        );
        self::assertNotEquals($withFirstLevelOnly, $withNextLevels);
    }

    /**
     * @return MockInterface|ProfessionZeroLevel
     */
    private function createZeroLevel()
    {
        $professionZeroLevel = $this->mockery(ProfessionZeroLevel::class);
        $professionZeroLevel->shouldReceive('getBasePropertyIncrement')
            ->with($this->type(PropertyCode::class))
            ->andReturn($baseProperty = $this->mockery(BaseProperty::class));
        $baseProperty->shouldReceive('getValue')
            ->andReturn(0);

        return $professionZeroLevel;
    }

    /**
     * @param string $professionCode
     * @return ProfessionFirstLevel|\Mockery\MockInterface
     */
    private function createFirstLevel($professionCode)
    {
        $firstLevel = $this->mockery(ProfessionFirstLevel::class);
        $this->addProfessionGetter($firstLevel, $professionCode);
        $firstLevel->shouldReceive('getLevelRank')
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->andReturn(1);
        $this->addPropertyIncrementGetters(
            $firstLevel,
            $strength = $this->isPrimaryProperty(PropertyCode::STRENGTH, $professionCode) ? 1 : 0,
            $agility = $this->isPrimaryProperty(PropertyCode::AGILITY, $professionCode) ? 1 : 0,
            $knack = $this->isPrimaryProperty(PropertyCode::KNACK, $professionCode) ? 1 : 0,
            $will = $this->isPrimaryProperty(PropertyCode::WILL, $professionCode) ? 1 : 0,
            $intelligence = $this->isPrimaryProperty(PropertyCode::INTELLIGENCE, $professionCode) ? 1 : 0,
            $charisma = $this->isPrimaryProperty(PropertyCode::CHARISMA, $professionCode) ? 1 : 0
        );
        $this->addPrimaryPropertiesAnswer($firstLevel, $professionCode);

        return $firstLevel;
    }

    private function addProfessionGetter(MockInterface $professionLevel, $professionCode)
    {
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
    }

    /*
     * EMPTY AFTER INITIALIZATION
     */

    /**
     * @test
     */
    public function I_got_everything_empty_or_zeroed_from_empty_new_levels()
    {
        $professionLevels = new ProfessionLevels(
            $zeroLevel = $this->createZeroLevel(),
            $firstLevel = $this->createFirstLevel(ProfessionCode::FIGHTER)
        );

        self::assertSame(0, $professionLevels->getNextLevelsStrengthModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::STRENGTH)));
        self::assertSame(0, $professionLevels->getNextLevelsAgilityModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::AGILITY)));
        self::assertSame(0, $professionLevels->getNextLevelsKnackModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::KNACK)));
        self::assertSame(0, $professionLevels->getNextLevelsWillModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::WILL)));
        self::assertSame(0, $professionLevels->getNextLevelsIntelligenceModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::INTELLIGENCE)));
        self::assertSame(0, $professionLevels->getNextLevelsCharismaModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::CHARISMA)));

        self::assertCount(0, $professionLevels->getProfessionNextLevels());
        self::assertEquals([$zeroLevel, $firstLevel], $professionLevels->getSortedProfessionLevels());
        $levelsFromIteration = [];
        foreach ($professionLevels as $professionLevel) {
            $levelsFromIteration[] = $professionLevel;
        }
        self::assertEquals($levelsFromIteration, $professionLevels->getSortedProfessionLevels());
    }

    /*
     * FIRST LEVELS
     */

    /**
     * @test
     */
    public function I_will_get_proper_value_of_first_level_properties()
    {
        $firstLevel = $this->createProfessionFirstLevel(ProfessionCode::FIGHTER);
        $this->addFirstLevelPropertyIncrementGetters($firstLevel, ProfessionCode::FIGHTER);
        $this->addPrimaryPropertiesAnswer($firstLevel, ProfessionCode::FIGHTER);
        $zeroLevel = $this->createZeroLevel();
        $professionLevels = $this->createProfessionLevelsWith($zeroLevel, $firstLevel);
        self::assertSame(
            $this->isPrimaryProperty(PropertyCode::STRENGTH, ProfessionCode::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelStrengthModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(PropertyCode::AGILITY, ProfessionCode::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelAgilityModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(PropertyCode::KNACK, ProfessionCode::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelKnackModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(PropertyCode::WILL, ProfessionCode::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelWillModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(PropertyCode::INTELLIGENCE, ProfessionCode::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelIntelligenceModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(PropertyCode::CHARISMA, ProfessionCode::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelCharismaModifier()
        );
    }

    private function addFirstLevelPropertyIncrementGetters(MockInterface $professionLevel, $professionCode)
    {
        $modifiers = [];
        foreach ($this->getPropertyNames() as $propertyName) {
            $modifiers[$propertyName] = $this->isPrimaryProperty($propertyName, $professionCode) ? 1 : 0;
        }
        $this->addPropertyIncrementGetters(
            $professionLevel,
            $modifiers[PropertyCode::STRENGTH],
            $modifiers[PropertyCode::AGILITY],
            $modifiers[PropertyCode::KNACK],
            $modifiers[PropertyCode::WILL],
            $modifiers[PropertyCode::INTELLIGENCE],
            $modifiers[PropertyCode::CHARISMA]
        );
    }

    private function addPropertyIncrementGetters(
        MockInterface $professionLevel,
        $strengthValue = 0,
        $agilityValue = 0,
        $knackValue = 0,
        $willValue = 0,
        $intelligenceValue = 0,
        $charismaValue = 0
    )
    {
        $professionLevel->shouldReceive('getStrengthIncrement')
            ->andReturn($strength = $this->mockery(Strength::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::STRENGTH))
            ->andReturn($strength);
        $this->addValueGetter($strength, $strengthValue);
        $this->addCodeGetter($strength, PropertyCode::STRENGTH);
        $professionLevel->shouldReceive('getAgilityIncrement')
            ->andReturn($agility = $this->mockery(Agility::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::AGILITY))
            ->andReturn($agility);
        $this->addValueGetter($agility, $agilityValue);
        $this->addCodeGetter($agility, PropertyCode::AGILITY);
        $professionLevel->shouldReceive('getKnackIncrement')
            ->andReturn($knack = $this->mockery(Knack::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::KNACK))
            ->andReturn($knack);
        $this->addValueGetter($knack, $knackValue);
        $this->addCodeGetter($knack, PropertyCode::KNACK);
        $professionLevel->shouldReceive('getWillIncrement')
            ->andReturn($will = $this->mockery(Will::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::WILL))
            ->andReturn($will);
        $this->addValueGetter($will, $willValue);
        $this->addCodeGetter($will, PropertyCode::WILL);
        $professionLevel->shouldReceive('getIntelligenceIncrement')
            ->andReturn($intelligence = $this->mockery(Intelligence::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::INTELLIGENCE))
            ->andReturn($intelligence);
        $this->addValueGetter($intelligence, $intelligenceValue);
        $this->addCodeGetter($intelligence, PropertyCode::INTELLIGENCE);
        $professionLevel->shouldReceive('getCharismaIncrement')
            ->andReturn($charisma = $this->mockery(Charisma::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::CHARISMA))
            ->andReturn($charisma);
        $this->addValueGetter($charisma, $charismaValue);
        $this->addCodeGetter($charisma, PropertyCode::CHARISMA);
    }

    private function addValueGetter(MockInterface $property, $value)
    {
        $property->shouldReceive('getValue')
            ->andReturn($value);
    }

    private function addCodeGetter(MockInterface $property, string $code)
    {
        $property->shouldReceive('getCode')
            ->andReturn(PropertyCode::getIt($code));
    }

    private function addPrimaryPropertiesAnswer(MockInterface $professionLevel, string $professionCode)
    {
        $modifiers = [];
        foreach ($this->getPropertyNames() as $propertyName) {
            $modifiers[$propertyName] = $this->isPrimaryProperty($propertyName, $professionCode) ? 1 : 0;
        }
        $primaryProperties = array_keys(array_filter($modifiers));

        foreach ($this->getPropertyNames() as $propertyName) {
            $professionLevel->shouldReceive('isPrimaryProperty')
                ->with(PropertyCode::getIt($propertyName))
                ->andReturn(in_array($propertyName, $primaryProperties, true));
        }
    }

    /**
     * @return array|string[]
     */
    private function getPropertyNames(): array
    {
        return PropertyCode::getBasePropertyPossibleValues();
    }

    private function addFirstLevelAnswer(MockInterface $professionLevel, $isFirstLevel)
    {
        $professionLevel->shouldReceive('isFirstLevel')
            ->andReturn($isFirstLevel);
    }

    private function addNextLevelAnswer(MockInterface $professionLevel, $isNextLevel)
    {
        $professionLevel->shouldReceive('isNextLevel')
            ->andReturn($isNextLevel);
    }

    /**
     * @param string $professionCode
     * @return ProfessionFirstLevel|ProfessionNextLevel|\Mockery\MockInterface
     */
    private function createProfessionFirstLevel($professionCode)
    {
        return $this->createProfessionLevel($professionCode, 1);
    }

    /**
     * @param string $professionCode
     * @param int $levelValue
     * @param ProfessionLevels|null $professionLevels
     * @return ProfessionFirstLevel|ProfessionNextLevel|MockInterface
     */
    private function createProfessionLevel($professionCode, $levelValue, ProfessionLevels $professionLevels = null)
    {
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getProfessionLevelClass($levelValue));
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $this->addFirstLevelAnswer($professionLevel, $levelValue === 1);
        $this->addNextLevelAnswer($professionLevel, $levelValue > 1);
        $professionLevel->shouldReceive('getLevelRank')
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->andReturn($levelValue);
        if ($professionLevels) {
            $professionLevel->shouldReceive('setProfessionLevels')
                ->with($professionLevels)
                ->atMost()
                ->once();
        }

        return $professionLevel;
    }

    /**
     * @param string $propertyName
     * @param string $professionCode
     * @return bool
     */
    private function isPrimaryProperty($propertyName, $professionCode): bool
    {
        return in_array($propertyName, $this->getPrimaryProperties($professionCode), true);
    }

    /**
     * @param string $professionCode
     * @return array
     * @throws \RuntimeException
     */
    private function getPrimaryProperties(string $professionCode): array
    {
        switch ($professionCode) {
            case ProfessionCode::FIGHTER :
                return [PropertyCode::STRENGTH, PropertyCode::AGILITY];
            case ProfessionCode::PRIEST :
                return [PropertyCode::WILL, PropertyCode::CHARISMA];
            case ProfessionCode::RANGER :
                return [PropertyCode::STRENGTH, PropertyCode::KNACK];
            case ProfessionCode::THEURGIST :
                return [PropertyCode::INTELLIGENCE, PropertyCode::CHARISMA];
            case ProfessionCode::THIEF :
                return [PropertyCode::KNACK, PropertyCode::AGILITY];
            case ProfessionCode::WIZARD :
                return [PropertyCode::WILL, PropertyCode::INTELLIGENCE];
            default :
                throw new \RuntimeException('Unknown profession name ' . var_export($professionCode, true));
        }
    }

    /**
     * @param ProfessionZeroLevel $zeroLevel
     * @param ProfessionFirstLevel $firstLevel
     * @return ProfessionLevels
     */
    private function createProfessionLevelsWith(ProfessionZeroLevel $zeroLevel, ProfessionFirstLevel $firstLevel): ProfessionLevels
    {
        $professionLevels = new ProfessionLevels($zeroLevel, $firstLevel);
        self::assertSame($firstLevel, $professionLevels->getFirstLevel());
        self::assertEquals([$zeroLevel, $firstLevel], $professionLevels->getSortedProfessionLevels());

        return $professionLevels;
    }

    /**
     * @test
     */
    public function I_can_add_profession_level()
    {
        $professionLevels = new ProfessionLevels(
            $zeroLevel = $this->createZeroLevel(),
            $firstLevel = $this->createFirstLevel(ProfessionCode::FIGHTER)
        );
        $nextLevel = $this->createProfessionLevel(ProfessionCode::FIGHTER, $levelValue = 2, $professionLevels);
        $this->addPropertyIncrementGetters(
            $nextLevel,
            $strength = $this->isPrimaryProperty(PropertyCode::STRENGTH, ProfessionCode::FIGHTER) ? 1 : 0,
            $agility = $this->isPrimaryProperty(PropertyCode::AGILITY, ProfessionCode::FIGHTER) ? 1 : 0,
            $knack = $this->isPrimaryProperty(PropertyCode::KNACK, ProfessionCode::FIGHTER) ? 1 : 0,
            $will = $this->isPrimaryProperty(PropertyCode::WILL, ProfessionCode::FIGHTER) ? 1 : 0,
            $intelligence = $this->isPrimaryProperty(PropertyCode::INTELLIGENCE, ProfessionCode::FIGHTER) ? 1 : 0,
            $charisma = $this->isPrimaryProperty(PropertyCode::CHARISMA, ProfessionCode::FIGHTER) ? 1 : 0
        );
        $this->addPrimaryPropertiesAnswer($nextLevel, ProfessionCode::FIGHTER);
        $professionLevels->addLevel($nextLevel);

        $strength += $firstLevel->getStrengthIncrement()->getValue();
        $agility += $firstLevel->getAgilityIncrement()->getValue();
        $knack += $firstLevel->getKnackIncrement()->getValue();
        $will += $firstLevel->getWillIncrement()->getValue();
        $intelligence += $firstLevel->getIntelligenceIncrement()->getValue();
        $charisma += $firstLevel->getCharismaIncrement()->getValue();

        self::assertSame($zeroLevel, $professionLevels->getZeroLevel());
        self::assertSame($firstLevel, $professionLevels->getFirstLevel());
        self::assertSame([$nextLevel], $professionLevels->getProfessionNextLevels());
        self::assertEquals([$zeroLevel, $firstLevel, $nextLevel], $professionLevels->getSortedProfessionLevels());
        self::assertEquals($nextLevel, $professionLevels->getCurrentLevel());

        self::assertSame($strength, $professionLevels->getStrengthModifierSummary());
        self::assertSame($strength, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::STRENGTH)));
        self::assertSame($agility, $professionLevels->getAgilityModifierSummary());
        self::assertSame($agility, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::AGILITY)));
        self::assertSame($knack, $professionLevels->getKnackModifierSummary());
        self::assertSame($knack, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::KNACK)));
        self::assertSame($will, $professionLevels->getWillModifierSummary());
        self::assertSame($will, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::WILL)));
        self::assertSame($intelligence, $professionLevels->getIntelligenceModifierSummary());
        self::assertSame($intelligence, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::INTELLIGENCE)));
        self::assertSame($charisma, $professionLevels->getCharismaModifierSummary());
        self::assertSame($charisma, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::CHARISMA)));

        self::assertSame($firstLevel->getStrengthIncrement()->getValue(), $professionLevels->getFirstLevelStrengthModifier());
        self::assertSame($firstLevel->getAgilityIncrement()->getValue(), $professionLevels->getFirstLevelAgilityModifier());
        self::assertSame($firstLevel->getKnackIncrement()->getValue(), $professionLevels->getFirstLevelKnackModifier());
        self::assertSame($firstLevel->getWillIncrement()->getValue(), $professionLevels->getFirstLevelWillModifier());
        self::assertSame($firstLevel->getIntelligenceIncrement()->getValue(), $professionLevels->getFirstLevelIntelligenceModifier());
        self::assertSame($firstLevel->getCharismaIncrement()->getValue(), $professionLevels->getFirstLevelCharismaModifier());

        self::assertSame($nextLevel->getStrengthIncrement()->getValue(), $professionLevels->getNextLevelsStrengthModifier());
        self::assertSame($nextLevel->getAgilityIncrement()->getValue(), $professionLevels->getNextLevelsAgilityModifier());
        self::assertSame($nextLevel->getKnackIncrement()->getValue(), $professionLevels->getNextLevelsKnackModifier());
        self::assertSame($nextLevel->getWillIncrement()->getValue(), $professionLevels->getNextLevelsWillModifier());
        self::assertSame($nextLevel->getIntelligenceIncrement()->getValue(), $professionLevels->getNextLevelsIntelligenceModifier());
        self::assertSame($nextLevel->getCharismaIncrement()->getValue(), $professionLevels->getNextLevelsCharismaModifier());
    }

    /**
     * @param int $levelValue
     * @return string
     */
    private function getProfessionLevelClass($levelValue): string
    {
        return (int)$levelValue === 1
            ? ProfessionFirstLevel::class
            : ProfessionNextLevel::class;
    }

    /*
     * MORE LEVELS
     */

    /**
     * @test
     */
    public function I_can_add_more_levels_of_same_profession()
    {
        $firstLevel = $this->createProfessionFirstLevel(ProfessionCode::FIGHTER);
        $this->addPrimaryPropertiesAnswer($firstLevel, ProfessionCode::FIGHTER);
        $this->addFirstLevelAnswer($firstLevel, true);
        $this->addNextLevelAnswer($firstLevel, false);
        $this->addPropertyIncrementGetters(
            $firstLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $zeroLevel = $this->createZeroLevel();
        $professionLevels = $this->createProfessionLevelsWith($zeroLevel, $firstLevel);

        self::assertCount(2, $professionLevels->getSortedProfessionLevels());
        self::assertSame($zeroLevel, $professionLevels->getZeroLevel());
        self::assertSame($firstLevel, $professionLevels->getFirstLevel());
        self::assertSame([$zeroLevel, $firstLevel], $professionLevels->getSortedProfessionLevels());

        $propertiesSummary = $firstLevelProperties = [];
        foreach ($this->getPropertyNames() as $propertyName) {
            $firstLevelProperties[$propertyName] = $propertiesSummary[$propertyName] = $$propertyName;
        }
        $secondLevel = $this->createProfessionLevel(ProfessionCode::FIGHTER, 2, $professionLevels);
        $this->addPropertyIncrementGetters(
            $secondLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $this->addPrimaryPropertiesAnswer($secondLevel, ProfessionCode::FIGHTER);
        $this->addNextLevelAnswer($secondLevel, true);
        $nextLevelProperties = [];
        foreach ($this->getPropertyNames() as $propertyName) {
            $nextLevelProperties[$propertyName] = $$propertyName;
            $propertiesSummary[$propertyName] += $$propertyName;
        }
        $professionLevels->addLevel($secondLevel);

        $thirdLevel = $this->createProfessionLevel(ProfessionCode::FIGHTER, 3, $professionLevels);
        $this->addPropertyIncrementGetters(
            $thirdLevel,
            $strength = ($this->isPrimaryProperty(PropertyCode::STRENGTH, ProfessionCode::FIGHTER) ? 7 : 0),
            $agility = ($this->isPrimaryProperty(PropertyCode::AGILITY, ProfessionCode::FIGHTER) ? 8 : 0),
            $knack = ($this->isPrimaryProperty(PropertyCode::KNACK, ProfessionCode::FIGHTER) ? 9 : 0),
            $will = ($this->isPrimaryProperty(PropertyCode::WILL, ProfessionCode::FIGHTER) ? 10 : 0),
            $intelligence = ($this->isPrimaryProperty(PropertyCode::INTELLIGENCE, ProfessionCode::FIGHTER) ? 11 : 0),
            $charisma = ($this->isPrimaryProperty(PropertyCode::CHARISMA, ProfessionCode::FIGHTER) ? 12 : 0)
        );
        $this->addPrimaryPropertiesAnswer($thirdLevel, ProfessionCode::FIGHTER);
        foreach ($this->getPropertyNames() as $propertyName) {
            $propertiesSummary[$propertyName] += $$propertyName;
            $nextLevelProperties[$propertyName] += $$propertyName;
        }
        $professionLevels->addLevel($thirdLevel);

        self::assertSame($zeroLevel, $professionLevels->getZeroLevel());
        self::assertSame($firstLevel, $professionLevels->getFirstLevel(), 'After adding a new level the old one is no more the first.');
        self::assertSame([$zeroLevel, $firstLevel, $secondLevel, $thirdLevel], $professionLevels->getSortedProfessionLevels());
        self::assertSame([$secondLevel, $thirdLevel], $professionLevels->getProfessionNextLevels());

        $levelsArray = [];
        foreach ($professionLevels as $professionLevel) {
            $levelsArray[] = $professionLevel;
        }
        self::assertEquals($professionLevels->getSortedProfessionLevels(), $levelsArray);
        self::assertSame($thirdLevel, $professionLevels->getCurrentLevel());
        self::assertSame(count($levelsArray), $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1 /* zero level */);

        self::assertSame($propertiesSummary[PropertyCode::STRENGTH], $professionLevels->getStrengthModifierSummary());
        self::assertSame($propertiesSummary[PropertyCode::AGILITY], $professionLevels->getAgilityModifierSummary());
        self::assertSame($propertiesSummary[PropertyCode::KNACK], $professionLevels->getKnackModifierSummary());
        self::assertSame($propertiesSummary[PropertyCode::WILL], $professionLevels->getWillModifierSummary());
        self::assertSame($propertiesSummary[PropertyCode::INTELLIGENCE], $professionLevels->getIntelligenceModifierSummary());
        self::assertSame($propertiesSummary[PropertyCode::CHARISMA], $professionLevels->getCharismaModifierSummary());

        self::assertSame($firstLevelProperties[PropertyCode::STRENGTH], $professionLevels->getFirstLevelStrengthModifier());
        self::assertSame($firstLevelProperties[PropertyCode::AGILITY], $professionLevels->getFirstLevelAgilityModifier());
        self::assertSame($firstLevelProperties[PropertyCode::KNACK], $professionLevels->getFirstLevelKnackModifier());
        self::assertSame($firstLevelProperties[PropertyCode::WILL], $professionLevels->getFirstLevelWillModifier());
        self::assertSame($firstLevelProperties[PropertyCode::INTELLIGENCE], $professionLevels->getFirstLevelIntelligenceModifier());
        self::assertSame($firstLevelProperties[PropertyCode::CHARISMA], $professionLevels->getFirstLevelCharismaModifier());

        self::assertSame($nextLevelProperties[PropertyCode::STRENGTH], $professionLevels->getNextLevelsStrengthModifier());
        self::assertSame($nextLevelProperties[PropertyCode::AGILITY], $professionLevels->getNextLevelsAgilityModifier());
        self::assertSame($nextLevelProperties[PropertyCode::KNACK], $professionLevels->getNextLevelsKnackModifier());
        self::assertSame($nextLevelProperties[PropertyCode::WILL], $professionLevels->getNextLevelsWillModifier());
        self::assertSame($nextLevelProperties[PropertyCode::INTELLIGENCE], $professionLevels->getNextLevelsIntelligenceModifier());
        self::assertSame($nextLevelProperties[PropertyCode::CHARISMA], $professionLevels->getNextLevelsCharismaModifier());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank
     */
    public function I_can_not_add_level_with_occupied_sequence()
    {
        $professionLevels = $this->createProfessionLevelsForChangeResistTest(ProfessionCode::FIGHTER);

        $levelsCount = count($professionLevels->getSortedProfessionLevels());
        self::assertGreaterThan(2, $levelsCount /* already occupied level rank to achieve conflict */);

        $anotherLevel = $this->createProfessionLevel(ProfessionCode::FIGHTER, $levelsCount - 1 /* zero level */);

        $professionLevels->addLevel($anotherLevel);
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank
     */
    public function I_can_not_add_level_with_out_of_sequence_rank()
    {
        $professionLevels = $this->createProfessionLevelsForChangeResistTest(ProfessionCode::FIGHTER);
        $levelsCount = count($professionLevels->getSortedProfessionLevels());
        self::assertGreaterThan(1, $levelsCount);

        $anotherLevel = $this->createProfessionLevel(ProfessionCode::FIGHTER, $levelsCount + 2 /* skipping a rank by one */, $professionLevels);

        $professionLevels->addLevel($anotherLevel);
    }

    private function createProfessionLevelsForChangeResistTest(string $professionCode): ProfessionLevels
    {
        $firstLevel = $this->createProfessionFirstLevel($professionCode);
        $this->addPrimaryPropertiesAnswer($firstLevel, $professionCode);
        $this->addFirstLevelAnswer($firstLevel, true);
        $this->addNextLevelAnswer($firstLevel, false);
        $this->addPropertyIncrementGetters(
            $firstLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $zeroLevel = $this->createZeroLevel();
        $professionLevels = $this->createProfessionLevelsWith($zeroLevel, $firstLevel);

        self::assertCount(2, $professionLevels->getSortedProfessionLevels());
        self::assertSame($zeroLevel, $professionLevels->getZeroLevel());
        self::assertSame($firstLevel, $professionLevels->getFirstLevel());
        self::assertEquals([$zeroLevel, $firstLevel], $professionLevels->getSortedProfessionLevels());

        $secondLevel = $this->createProfessionLevel($professionCode, 2, $professionLevels);
        $this->addPropertyIncrementGetters(
            $secondLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $this->addPrimaryPropertiesAnswer($secondLevel, $professionCode);
        $this->addNextLevelAnswer($secondLevel, true);

        $professionLevels->addLevel($secondLevel);

        $thirdLevel = $this->createProfessionLevel($professionCode, 3, $professionLevels);
        $this->addPropertyIncrementGetters(
            $thirdLevel,
            $strength = ($this->isPrimaryProperty(PropertyCode::STRENGTH, $professionCode) ? 7 : 0),
            $agility = ($this->isPrimaryProperty(PropertyCode::AGILITY, $professionCode) ? 8 : 0),
            $knack = ($this->isPrimaryProperty(PropertyCode::KNACK, $professionCode) ? 9 : 0),
            $will = ($this->isPrimaryProperty(PropertyCode::WILL, $professionCode) ? 10 : 0),
            $intelligence = ($this->isPrimaryProperty(PropertyCode::INTELLIGENCE, $professionCode) ? 11 : 0),
            $charisma = ($this->isPrimaryProperty(PropertyCode::CHARISMA, $professionCode) ? 12 : 0)
        );
        $this->addPrimaryPropertiesAnswer($thirdLevel, $professionCode);
        $professionLevels->addLevel($thirdLevel);

        return $professionLevels;
    }

    /*
     * ONLY SINGLE PROFESSION IS ALLOWED
     */

    /**
     * @test
     */
    public function I_can_not_mix_professions()
    {
        $professionLevels = $this->createProfessionLevelsForMixTest(ProfessionCode::FIGHTER);
        /** @var ProfessionFirstLevel|\Mockery\MockInterface $firstLevel */
        $firstLevel = $professionLevels->getFirstLevel();
        self::assertInstanceOf(ProfessionFirstLevel::class, $firstLevel);

        $otherLevels = $this->getLevelsExcept($firstLevel);
        self::assertNotEmpty($otherLevels);

        foreach ($otherLevels as $professionCode => $otherProfessionLevel) {
            try {
                $professionLevels->addLevel($otherProfessionLevel);
                self::fail(
                    "Adding $professionCode to levels already set to {$firstLevel->getProfession()->getValue()} should throw exception."
                );
            } catch (MultiProfessionsAreProhibited $exception) {
                self::assertNotNull($exception);
            }
        }
    }

    /**
     * @param string $professionCode
     * @return ProfessionLevels
     */
    private function createProfessionLevelsForMixTest(string $professionCode): ProfessionLevels
    {
        $professionLevels = new ProfessionLevels(
            $this->createZeroLevel(),
            $firstLevel = $this->createFirstLevel($professionCode)
        );

        return $professionLevels;
    }

    /**
     * @param ProfessionLevel $excludedProfession
     * @return \Mockery\MockInterface[]|ProfessionFirstLevel[]|ProfessionNextLevel[]
     */
    private function getLevelsExcept(ProfessionLevel $excludedProfession): array
    {
        $professionLevels = $this->buildProfessionLevels();

        return array_filter(
            $professionLevels,
            function (ProfessionLevel $level) use ($excludedProfession) {
                return $level->getProfession()->getValue() !== $excludedProfession->getProfession()->getValue();
            }
        );
    }

    /**
     * @return array|ProfessionNextLevel[]
     */
    private function buildProfessionLevels(): array
    {
        $professions = [
            ProfessionCode::FIGHTER => Fighter::class,
            ProfessionCode::PRIEST => Priest::class,
            ProfessionCode::RANGER => Ranger::class,
            ProfessionCode::THEURGIST => Theurgist::class,
            ProfessionCode::THIEF => Thief::class,
            ProfessionCode::WIZARD => Wizard::class,
        ];
        $professionLevels = [];
        foreach ($professions as $professionCode => $professionClass) {
            $professionLevels[$professionCode] = $level = $this->mockery(ProfessionNextLevel::class);
            $profession = $this->mockery($professionClass);
            $profession->shouldReceive('getValue')
                ->andReturn($professionCode);
            $level->shouldReceive('getProfession')
                ->andReturn($profession);
        }

        return $professionLevels;
    }

    /*
     * SAME PROPERTY INCREMENT IN A ROW
     */

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighPrimaryPropertyIncrease
     */
    public function I_can_not_increase_primary_property_three_times_in_a_row()
    {
        try {
            $firstLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(ProfessionCode::FIGHTER, 1);
            $zeroLevel = $this->createZeroLevel();
            // the first level does not come to property increment check
            $professionLevels = new ProfessionLevels($zeroLevel, $firstLevel);

            // the second level will be taken into account on check of fourth level
            $secondLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(
                ProfessionCode::FIGHTER,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                true, // first primary property increment
                false, // no second primary property increment
                $professionLevels
            );
            $professionLevels->addLevel($secondLevel);

            // with both primary properties increased
            $thirdLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(
                ProfessionCode::FIGHTER,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                false, // no first primary property increment
                true, // second primary property increment
                $professionLevels
            );
            $professionLevels->addLevel($thirdLevel);

            // again with both primary properties increased
            $fourthLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(
                ProfessionCode::FIGHTER,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                true, // first primary property increment
                true, // second primary property increment
                $professionLevels
            );
            $professionLevels->addLevel($fourthLevel); //should pass
        } catch (\Exception $exception) {
            self::fail(
                'No exception should happen this far: ' . $exception->getMessage()
                . ' (' . $exception->getTraceAsString() . ')'
            );

            return;
        }

        // and again with both primary properties increased
        $fifthLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(
            ProfessionCode::FIGHTER,
            $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1
        );
        $professionLevels->addLevel($fifthLevel); // should fail
    }

    private function createProfessionLevelWithPrimaryPropertiesIncreased(
        $professionCode,
        $levelValue,
        $increaseFirstPrimaryProperty = true,
        $increaseSecondPrimaryProperty = true,
        ProfessionLevels $professionLevels = null
    )
    {
        $professionLevel = $this->createProfessionLevel($professionCode, $levelValue, $professionLevels);
        $propertyIncrements = [];
        $isFirst = true;
        foreach ($this->getPropertyNames() as $propertyCode) {
            $increment = $this->isPrimaryProperty($propertyCode, $professionCode) ? 1 : 0;
            if ($increment) {
                if ($isFirst) {
                    $isFirst = false;
                    $increment &= $increaseFirstPrimaryProperty;
                } else {
                    $increment &= $increaseSecondPrimaryProperty;
                }
            }
            $propertyIncrements[$propertyCode] = $increment;
        }
        $this->addPropertyIncrementGetters(
            $professionLevel,
            $propertyIncrements[PropertyCode::STRENGTH],
            $propertyIncrements[PropertyCode::AGILITY],
            $propertyIncrements[PropertyCode::KNACK],
            $propertyIncrements[PropertyCode::WILL],
            $propertyIncrements[PropertyCode::INTELLIGENCE],
            $propertyIncrements[PropertyCode::CHARISMA]
        );
        $this->addPrimaryPropertiesAnswer($professionLevel, $professionCode);

        return $professionLevel;
    }

    /**
     * @param string $professionCode
     * @test
     * dataProvider provideProfessionCode
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighSecondaryPropertyIncrease
     */
    public function I_can_not_increase_secondary_property_two_times_in_a_row($professionCode = 'fighter')
    {
        try {
            $firstLevel = $this->createProfessionLevelWithSecondaryPropertiesIncreased($professionCode, 1);
            // the first level does not come to property increment check
            $zeroLevel = $this->createZeroLevel();
            $professionLevels = new ProfessionLevels($zeroLevel, $firstLevel);

            // the second level will be taken into account on check of third level
            $secondLevel = $this->createProfessionLevelWithSecondaryPropertiesIncreased(
                $professionCode,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                false, // without secondary properties increment
                $professionLevels
            );
            $professionLevels->addLevel($secondLevel);

            $thirdLevel = $this->createProfessionLevelWithSecondaryPropertiesIncreased(
                $professionCode,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                true, // with secondary properties increment
                $professionLevels
            );
            $professionLevels->addLevel($thirdLevel); // should pass
        } catch (\Exception $exception) {
            self::fail(
                "No exception should happen this far: {$exception->getMessage()} ({$exception->getTraceAsString()})"
            );

            return;
        }
        $fourthLevel = $this->createProfessionLevelWithSecondaryPropertiesIncreased(
            $professionCode,
            $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
            true, // with secondary properties increment
            $professionLevels
        );
        $professionLevels->addLevel($fourthLevel); // should fail
    }

    private function createProfessionLevelWithSecondaryPropertiesIncreased(
        $professionCode,
        $levelValue,
        $increaseSecondaryProperties = true,
        ProfessionLevels $professionLevels = null
    )
    {
        $professionLevel = $this->createProfessionLevel($professionCode, $levelValue, $professionLevels);
        $propertyIncrements = [];
        foreach ($this->getPropertyNames() as $propertyCode) {
            $increment = $increaseSecondaryProperties
            && !$this->isPrimaryProperty($propertyCode, $professionCode) ? 1 : 0;
            $propertyIncrements[$propertyCode] = $increment;
        }
        $this->addPropertyIncrementGetters(
            $professionLevel,
            $propertyIncrements[PropertyCode::STRENGTH],
            $propertyIncrements[PropertyCode::AGILITY],
            $propertyIncrements[PropertyCode::KNACK],
            $propertyIncrements[PropertyCode::WILL],
            $propertyIncrements[PropertyCode::INTELLIGENCE],
            $propertyIncrements[PropertyCode::CHARISMA]
        );
        $this->addPrimaryPropertiesAnswer($professionLevel, $professionCode);

        return $professionLevel;
    }
}