<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\BaseProperties\Property;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Professions\Profession;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use Mockery\MockInterface;

class ProfessionFirstLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function I_can_create_it(string $professionCode): void
    {
        $professionFirstLevel = ProfessionFirstLevel::createFirstLevel(
            $this->createProfession($professionCode),
            $levelUpAt = new \DateTimeImmutable('2004-01-01')
        );
        /** @var ProfessionLevel $professionFirstLevel */
        self::assertSame($professionCode, $professionFirstLevel->getProfession()->getValue());
        self::assertTrue($professionFirstLevel->isFirstLevel());
        self::assertFalse($professionFirstLevel->isNextLevel());
        foreach (PropertyCode::getBasePropertyPossibleValues() as $propertyValue) {
            self::assertSame(
                $this->isPrimaryProperty($propertyValue, $professionCode),
                $professionFirstLevel->isPrimaryProperty(PropertyCode::getIt($propertyValue)),
                "$professionCode - $propertyValue"
            );
            self::assertInstanceOf(
                $this->getPropertyClassByCode($propertyValue),
                $propertyIncrement = $professionFirstLevel->getBasePropertyIncrement(PropertyCode::getIt($propertyValue))
            );
            self::assertSame(
                $this->isPrimaryProperty($propertyValue, $professionCode) ? 1 : 0,
                $propertyIncrement->getValue()
            );
        }
        self::assertSame($levelUpAt, $professionFirstLevel->getLevelUpAt());
    }

    public function provideProfessionCode(): array
    {
        return [
            [ProfessionCode::FIGHTER],
            [ProfessionCode::THIEF],
            [ProfessionCode::RANGER],
            [ProfessionCode::WIZARD],
            [ProfessionCode::THEURGIST],
            [ProfessionCode::PRIEST]
        ];
    }

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function I_can_get_level_details(string $professionCode): void
    {
        $reflectionClass = new \ReflectionClass(ProfessionFirstLevel::class);
        $constructor = $reflectionClass->getMethod('__construct');
        $constructor->setAccessible(true);
        $professionLevel = $reflectionClass->newInstanceWithoutConstructor();
        /** @var ProfessionLevel $professionLevel */
        $constructor->invoke(
            $professionLevel,
            $profession = $this->createProfession($professionCode),
            $levelRank = $this->createLevelRank(),
            $strengthIncrement = $this->createStrength($professionCode),
            $agilityIncrement = $this->createAgility($professionCode),
            $knackIncrement = $this->createKnack($professionCode),
            $willIncrement = $this->createWill($professionCode),
            $intelligenceIncrement = $this->createIntelligence($professionCode),
            $charismaIncrement = $this->createCharisma($professionCode),
            $levelUpAt = new \DateTimeImmutable());
        self::assertSame($profession, $professionLevel->getProfession());
        self::assertSame($levelRank, $professionLevel->getLevelRank());
        self::assertSame($strengthIncrement, $professionLevel->getStrengthIncrement());
        self::assertSame($agilityIncrement, $professionLevel->getAgilityIncrement());
        self::assertSame($knackIncrement, $professionLevel->getKnackIncrement());
        self::assertSame($intelligenceIncrement, $professionLevel->getIntelligenceIncrement());
        self::assertSame($charismaIncrement, $professionLevel->getCharismaIncrement());
        self::assertSame($willIncrement, $professionLevel->getWillIncrement());
        self::assertSame($levelUpAt, $professionLevel->getLevelUpAt());
    }

    /**
     * @param int $rankValue
     * @return LevelRank
     */
    private function createLevelRank(int $rankValue = 1): LevelRank
    {
        /** @var LevelRank|\Mockery\MockInterface $levelRank */
        $levelRank = $this->mockery(LevelRank::class);
        $levelRank->shouldReceive('getValue')
            ->andReturn($rankValue);
        $levelRank->shouldReceive('isFirstLevel')
            ->andReturn($rankValue === 1);
        $levelRank->shouldReceive('isNextLevel')
            ->andReturn($rankValue > 1);

        return $levelRank;
    }

    /**
     * @param string $professionCode
     * @param int|null $propertyValue = null
     * @return Strength
     */
    private function createStrength(string $professionCode, int $propertyValue = null): Strength
    {
        return $this->createProperty($professionCode, Strength::class, PropertyCode::STRENGTH, $propertyValue);
    }

    /**
     * @param string $professionCode
     * @param string $propertyClass
     * @param string $propertyCode
     * @param string|null $propertyValue = null
     * @return MockInterface|Property|Strength|Agility|Knack|Will|Intelligence|Charisma
     */
    private function createProperty(string $professionCode, string $propertyClass, string $propertyCode, $propertyValue = null): Property
    {
        $property = \Mockery::mock($propertyClass);
        $this->addPropertyExpectation($professionCode, $property, $propertyCode, $propertyValue);

        return $property;
    }

    private function addPropertyExpectation(
        string $professionCode,
        MockInterface $property,
        string $propertyCode,
        int $propertyValue = null
    ): void
    {
        $property->shouldReceive('getValue')
            ->andReturnUsing(function () use ($propertyValue, $propertyCode, $professionCode) {
                if ($propertyValue === null) {
                    return $this->isPrimaryProperty($propertyCode, $professionCode)
                        ? 1
                        : 0;
                }

                return $propertyValue;
            });
        $property->shouldReceive('getCode')
            ->andReturn(PropertyCode::getIt($propertyCode));
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Agility
     */
    private function createAgility(string $professionCode, int $value = null): Agility
    {
        return $this->createProperty($professionCode, Agility::class, PropertyCode::AGILITY, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Knack
     */
    private function createKnack(string $professionCode, int $value = null): Knack
    {
        return $this->createProperty($professionCode, Knack::class, PropertyCode::KNACK, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Will
     */
    private function createWill(string $professionCode, int $value = null): Will
    {
        return $this->createProperty($professionCode, Will::class, PropertyCode::WILL, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Intelligence
     */
    private function createIntelligence(string $professionCode, int $value = null): Intelligence
    {
        return $this->createProperty($professionCode, Intelligence::class, PropertyCode::INTELLIGENCE, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Charisma
     */
    private function createCharisma(string $professionCode, int $value = null): Charisma
    {
        return $this->createProperty($professionCode, Charisma::class, PropertyCode::CHARISMA, $value);
    }

    private function getPropertyClassByCode($propertyCode): string
    {
        switch ($propertyCode) {
            case PropertyCode::STRENGTH :
                return Strength::class;
            case PropertyCode::AGILITY :
                return Agility::class;
            case PropertyCode::KNACK :
                return Knack::class;
            case PropertyCode::WILL :
                return Will::class;
            case PropertyCode::INTELLIGENCE :
                return Intelligence::class;
            case PropertyCode::CHARISMA :
                return Charisma::class;
            default :
                throw new \LogicException('Where did you get that? ' . $propertyCode);
        }
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_create_it_with_default_level_up_at(): void
    {
        $professionFirstLevel = ProfessionFirstLevel::createFirstLevel(
            $this->createProfession(ProfessionCode::FIGHTER)
        );
        $levelUpAt = $professionFirstLevel->getLevelUpAt();
        self::assertSame(\time(), $levelUpAt->getTimestamp());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelRank
     * @throws \ReflectionException
     */
    public function I_can_not_create_higher_first_level_than_one(): void
    {
        new PublicConstructorProfessionFistLevel(
            $this->createProfession(ProfessionCode::FIGHTER),
            $this->createLevelRank(2),
            $this->createStrength(ProfessionCode::FIGHTER),
            $this->createAgility(ProfessionCode::FIGHTER),
            $this->createKnack(ProfessionCode::FIGHTER),
            $this->createWill(ProfessionCode::FIGHTER),
            $this->createIntelligence(ProfessionCode::FIGHTER),
            $this->createCharisma(ProfessionCode::FIGHTER)
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelRank
     * @throws \ReflectionException
     */
    public function I_can_not_create_lesser_first_level_than_one(): void
    {
        new PublicConstructorProfessionFistLevel(
            $this->createProfession(ProfessionCode::FIGHTER),
            $this->createLevelRank(0),
            $this->createStrength(ProfessionCode::FIGHTER),
            $this->createAgility(ProfessionCode::FIGHTER),
            $this->createKnack(ProfessionCode::FIGHTER),
            $this->createWill(ProfessionCode::FIGHTER),
            $this->createIntelligence(ProfessionCode::FIGHTER),
            $this->createCharisma(ProfessionCode::FIGHTER)
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelPropertyValue
     * @dataProvider provideTooHighFirstLevelPropertiesOneByOne
     * @throws \ReflectionException
     *
     * @param string $professionCode
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_not_use_greater_than_allowed_first_level_property(
        string $professionCode,
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence,
        int $charisma
    ): void
    {
        new PublicConstructorProfessionFistLevel(
            $this->createProfession($professionCode),
            $this->createLevelRank(),
            $this->createStrength($professionCode, $strength),
            $this->createAgility($professionCode, $agility),
            $this->createKnack($professionCode, $knack),
            $this->createWill($professionCode, $will),
            $this->createIntelligence($professionCode, $intelligence),
            $this->createCharisma($professionCode, $charisma)
        );
    }

    public function provideTooHighFirstLevelPropertiesOneByOne(): array
    {
        $values = [];
        foreach ($this->getProfessionCodes() as $professionCode) {
            $singleTestValuesPattern = [
                $this->createStrength($professionCode)->getValue(),
                $this->createAgility($professionCode)->getValue(),
                $this->createKnack($professionCode)->getValue(),
                $this->createWill($professionCode)->getValue(),
                $this->createIntelligence($professionCode)->getValue(),
                $this->createCharisma($professionCode)->getValue(),
            ];
            foreach ($singleTestValuesPattern as $index => $value) {
                $singleTestValues = $singleTestValuesPattern;
                $singleTestValues[$index] = $value + 1;
                array_unshift($singleTestValues, $professionCode);
                $values[] = $singleTestValues;
            }
        }

        return $values;
    }

    private function getProfessionCodes(): array
    {
        return [
            ProfessionCode::FIGHTER,
            ProfessionCode::THIEF,
            ProfessionCode::RANGER,
            ProfessionCode::WIZARD,
            ProfessionCode::THEURGIST,
            ProfessionCode::PRIEST,
        ];
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelPropertyValue
     * @dataProvider getTooLowFirstLevelPropertiesOneByOne
     * @throws \ReflectionException
     *
     * @param $professionCode
     * @param $strength
     * @param $agility
     * @param $knack
     * @param $will
     * @param $intelligence
     * @param $charisma
     */
    public function I_can_not_use_lesser_than_allowed_first_level_property(
        string $professionCode,
        int $strength,
        int $agility,
        int $knack,
        int $will,
        int $intelligence,
        int $charisma
    ): void
    {
        new PublicConstructorProfessionFistLevel(
            $this->createProfession($professionCode),
            $this->createLevelRank(),
            $this->createStrength($professionCode, $strength),
            $this->createAgility($professionCode, $agility),
            $this->createKnack($professionCode, $knack),
            $this->createWill($professionCode, $will),
            $this->createIntelligence($professionCode, $intelligence),
            $this->createCharisma($professionCode, $charisma)
        );
    }

    public function getTooLowFirstLevelPropertiesOneByOne(): array
    {
        $values = [];
        foreach ($this->getProfessionCodes() as $professionCode) {
            $singleTestValuesPattern = [
                $this->createStrength($professionCode)->getValue(),
                $this->createAgility($professionCode)->getValue(),
                $this->createKnack($professionCode)->getValue(),
                $this->createWill($professionCode)->getValue(),
                $this->createIntelligence($professionCode)->getValue(),
                $this->createCharisma($professionCode)->getValue(),
            ];
            foreach ($singleTestValuesPattern as $index => $value) {
                $singleTestValues = $singleTestValuesPattern;
                $singleTestValues[$index] = $value - 1;
                array_unshift($singleTestValues, $professionCode);
                $values[] = $singleTestValues;
            }
        }

        return $values;
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\UnknownBaseProperty
     * @throws \ReflectionException
     */
    public function I_am_stopped_on_use_of_unknown_property_code(): void
    {
        ProfessionFirstLevel::createFirstLevel(
            $this->createProfession(ProfessionCode::FIGHTER)
        )->getBasePropertyIncrement($this->createPropertyCode('invalid'));
    }

    /**
     * @param string $value
     * @return MockInterface|PropertyCode
     */
    private function createPropertyCode(string $value)
    {
        $propertyCode = $this->mockery(PropertyCode::class);
        $propertyCode->shouldReceive('getValue')
            ->andReturn($value);

        return $propertyCode;
    }

}

/** inner */
class PublicConstructorProfessionFistLevel extends ProfessionFirstLevel
{
    public function __construct(
        Profession $profession,
        LevelRank $levelRank,
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement,
        \DateTimeImmutable $levelUpAt = null
    )
    {
        parent::__construct(
            $profession,
            $levelRank,
            $strengthIncrement,
            $agilityIncrement,
            $knackIncrement,
            $willIncrement,
            $intelligenceIncrement,
            $charismaIncrement,
            $levelUpAt
        );
    }
}