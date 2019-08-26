<?php declare(strict_types=1);

namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\BaseProperties\Property;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionNextLevel;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use Mockery\MockInterface;

class ProfessionNextLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     * @throws \ReflectionException
     */
    public function I_can_create_it(string $professionCode)
    {
        $professionNextLevel = ProfessionNextLevel::createNextLevel(
            $profession = $this->createProfession($professionCode),
            $levelRank = $this->createLevelRank(2),
            $strengthIncrement = $this->createStrength($professionCode),
            $agilityIncrement = $this->createAgility($professionCode),
            $knackIncrement = $this->createKnack($professionCode),
            $willIncrement = $this->createWill($professionCode),
            $intelligenceIncrement = $this->createIntelligence($professionCode),
            $charismaIncrement = $this->createCharisma($professionCode),
            $levelUpAt = new \DateTimeImmutable()
        );
        /** @var ProfessionLevel $professionNextLevel */
        self::assertSame($professionCode, $professionNextLevel->getProfession()->getValue());
        self::assertFalse($professionNextLevel->isFirstLevel());
        self::assertTrue($professionNextLevel->isNextLevel());
        self::assertSame($levelRank, $professionNextLevel->getLevelRank());
        foreach (PropertyCode::getBasePropertyPossibleValues() as $propertyValue) {
            self::assertSame(
                $this->isPrimaryProperty($propertyValue, $professionCode),
                $professionNextLevel->isPrimaryProperty(PropertyCode::getIt($propertyValue))
            );
            self::assertInstanceOf(
                $this->getPropertyClassByCode($propertyValue),
                $propertyIncrement = $professionNextLevel->getBasePropertyIncrement(PropertyCode::getIt($propertyValue))
            );
            self::assertSame(
                $this->isPrimaryProperty($propertyValue, $professionCode) ? 1 : 0,
                $propertyIncrement->getValue()
            );
        }
        self::assertSame($levelUpAt, $professionNextLevel->getLevelUpAt());
    }

    public function provideProfessionCode(): array
    {
        return [
            [ProfessionCode::FIGHTER],
            [ProfessionCode::THIEF],
            [ProfessionCode::RANGER],
            [ProfessionCode::WIZARD],
            [ProfessionCode::THEURGIST],
            [ProfessionCode::PRIEST],
        ];
    }

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     */
    public function I_can_get_level_details(string $professionCode)
    {
        /** @var ProfessionLevel $professionNextLevel */
        $professionNextLevel = ProfessionNextLevel::createNextLevel(
            $profession = $this->createProfession($professionCode),
            $levelRank = $this->createLevelRank(2),
            $strengthIncrement = $this->createStrength($professionCode),
            $agilityIncrement = $this->createAgility($professionCode),
            $knackIncrement = $this->createKnack($professionCode),
            $willIncrement = $this->createWill($professionCode),
            $intelligenceIncrement = $this->createIntelligence($professionCode),
            $charismaIncrement = $this->createCharisma($professionCode),
            $levelUpAt = new \DateTimeImmutable()
        );
        self::assertSame($profession, $professionNextLevel->getProfession());
        self::assertSame($levelRank, $professionNextLevel->getLevelRank());
        self::assertSame($strengthIncrement, $professionNextLevel->getStrengthIncrement());
        self::assertSame($agilityIncrement, $professionNextLevel->getAgilityIncrement());
        self::assertSame($knackIncrement, $professionNextLevel->getKnackIncrement());
        self::assertSame($intelligenceIncrement, $professionNextLevel->getIntelligenceIncrement());
        self::assertSame($charismaIncrement, $professionNextLevel->getCharismaIncrement());
        self::assertSame($willIncrement, $professionNextLevel->getWillIncrement());
        self::assertSame($levelUpAt, $professionNextLevel->getLevelUpAt());
    }

    /**
     * @param string $rankValue
     * @return LevelRank
     */
    private function createLevelRank($rankValue)
    {
        /** @var LevelRank|\Mockery\MockInterface $levelRank */
        $levelRank = $this->mockery(LevelRank::class);
        $levelRank->shouldReceive('getValue')
            ->andReturn($rankValue);
        $levelRank->shouldReceive('isFirstLevel')
            ->andReturn((int)$rankValue === 1);
        $levelRank->shouldReceive('isNextLevel')
            ->andReturn($rankValue > 1);

        return $levelRank;
    }

    /**
     * @param string $professionCode
     * @param int|null $propertyValue = null
     * @return Strength
     */
    private function createStrength($professionCode, $propertyValue = null)
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
    private function createProperty(string $professionCode, string $propertyClass, string $propertyCode, string $propertyValue = null): Property
    {
        $property = $this->mockery($propertyClass);
        $this->addPropertyExpectation($professionCode, $property, $propertyCode, $propertyValue);

        return $property;
    }

    private function addPropertyExpectation(
        string $professionCode,
        MockInterface $property,
        string $propertyName,
        string $propertyValue = null
    )
    {
        $property->shouldReceive('getValue')
            ->andReturnUsing(function () use ($propertyValue, $propertyName, $professionCode) {
                if ($propertyValue !== null) {
                    return $propertyValue;
                }

                return $this->isPrimaryProperty($propertyName, $professionCode)
                    ? 1
                    : 0;
            });
        $property->shouldReceive('getCode')
            ->andReturn($propertyName);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Agility
     */
    private function createAgility(string $professionCode, int $value = null)
    {
        return $this->createProperty($professionCode, Agility::class, PropertyCode::AGILITY, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Knack
     */
    private function createKnack(string $professionCode, int $value = null)
    {
        return $this->createProperty($professionCode, Knack::class, PropertyCode::KNACK, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Will
     */
    private function createWill(string $professionCode, int $value = null)
    {
        return $this->createProperty($professionCode, Will::class, PropertyCode::WILL, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Intelligence
     */
    private function createIntelligence($professionCode, $value = null)
    {
        return $this->createProperty($professionCode, Intelligence::class, PropertyCode::INTELLIGENCE, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Charisma
     */
    private function createCharisma($professionCode, $value = null)
    {
        return $this->createProperty($professionCode, Charisma::class, PropertyCode::CHARISMA, $value);
    }

    private function getPropertyClassByCode(string $propertyCode): string
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
     */
    public function I_can_create_it_with_default_level_up_at()
    {
        $ProfessionNextLevel = ProfessionNextLevel::createNextLevel(
            $profession = $this->createProfession(ProfessionCode::FIGHTER),
            $levelRank = $this->createLevelRank(2),
            $strengthIncrement = $this->createStrength(ProfessionCode::FIGHTER),
            $agilityIncrement = $this->createAgility(ProfessionCode::FIGHTER),
            $knackIncrement = $this->createKnack(ProfessionCode::FIGHTER),
            $willIncrement = $this->createWill(ProfessionCode::FIGHTER),
            $intelligenceIncrement = $this->createIntelligence(ProfessionCode::FIGHTER),
            $charismaIncrement = $this->createCharisma(ProfessionCode::FIGHTER)
        );
        $levelUpAt = $ProfessionNextLevel->getLevelUpAt();
        self::assertInstanceOf(\DateTimeImmutable::class, $levelUpAt);
        self::assertSame(time(), $levelUpAt->getTimestamp());
    }

    /**
     * @test
     */
    public function I_can_not_create_higher_next_level_than_twenty()
    {
        $this->expectException(\DrdPlus\Person\ProfessionLevels\Exceptions\MaximumLevelExceeded::class);
        ProfessionNextLevel::createNextLevel(
            $this->createProfession(ProfessionCode::FIGHTER),
            $this->createLevelRank(22),
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
     */
    public function I_can_not_create_lesser_next_level_than_two()
    {
        $this->expectException(\DrdPlus\Person\ProfessionLevels\Exceptions\MinimumLevelExceeded::class);
        ProfessionNextLevel::createNextLevel(
            $this->createProfession(ProfessionCode::FIGHTER),
            $this->createLevelRank(1),
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
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     */
    public function I_can_not_create_next_level_with_too_high_properties_sum($professionCode)
    {
        $this->expectException(\DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum::class);
        $this->expectExceptionMessageRegExp('" 2, got 6$"');
        ProfessionNextLevel::createNextLevel(
            $this->createProfession($professionCode),
            $levelRank = LevelRank::getIt(2),
            Strength::getIt(1),
            Agility::getIt(1),
            Knack::getIt(1),
            Will::getIt(1),
            Intelligence::getIt(1),
            Charisma::getIt(1)
        );
    }

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     */
    public function I_can_not_create_next_level_with_too_low_properties_sum($professionCode)
    {
        $this->expectException(\DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum::class);
        $this->expectExceptionMessageRegExp('" 2, got 0$"');
        ProfessionNextLevel::createNextLevel(
            $this->createProfession($professionCode),
            $levelRank = LevelRank::getIt(2),
            Strength::getIt(0),
            Agility::getIt(0),
            Knack::getIt(0),
            Will::getIt(0),
            Intelligence::getIt(0),
            Charisma::getIt(0)
        );
    }

    /**
     * @param string $propertyCodeToNegative
     *
     * @test
     * @dataProvider providePropertyCodeOneByOne
     */
    public function I_can_not_create_next_level_with_negative_property($propertyCodeToNegative)
    {
        $this->expectException(\DrdPlus\Person\ProfessionLevels\Exceptions\NegativeNextLevelProperty::class);
        ProfessionNextLevel::createNextLevel(
            $this->createProfession(ProfessionCode::FIGHTER),
            $levelRank = LevelRank::getIt(2),
            Strength::getIt($propertyCodeToNegative === PropertyCode::STRENGTH ? -1 : 0),
            Agility::getIt($propertyCodeToNegative === PropertyCode::AGILITY ? -1 : 0),
            Knack::getIt($propertyCodeToNegative === PropertyCode::KNACK ? -1 : 0),
            Will::getIt($propertyCodeToNegative === PropertyCode::WILL ? -1 : 0),
            Intelligence::getIt($propertyCodeToNegative === PropertyCode::INTELLIGENCE ? -1 : 0),
            Charisma::getIt($propertyCodeToNegative === PropertyCode::CHARISMA ? -1 : 0)
        );
    }

    public function providePropertyCodeOneByOne()
    {
        return [
            [PropertyCode::STRENGTH],
            [PropertyCode::AGILITY],
            [PropertyCode::KNACK],
            [PropertyCode::WILL],
            [PropertyCode::INTELLIGENCE],
            [PropertyCode::CHARISMA],
        ];
    }

    /**
     * @param string $propertyCodeTooHigh
     *
     * @test
     * @dataProvider providePropertyCodeOneByOne
     */
    public function I_can_not_create_next_level_with_too_high_property_increment($propertyCodeTooHigh)
    {
        $this->expectException(\DrdPlus\Person\ProfessionLevels\Exceptions\TooHighNextLevelPropertyIncrement::class);
        ProfessionNextLevel::createNextLevel(
            $this->createProfession(ProfessionCode::FIGHTER),
            $levelRank = LevelRank::getIt(2),
            Strength::getIt($propertyCodeTooHigh === PropertyCode::STRENGTH ? 2 : 0),
            Agility::getIt($propertyCodeTooHigh === PropertyCode::AGILITY ? 2 : 0),
            Knack::getIt($propertyCodeTooHigh === PropertyCode::KNACK ? 2 : 0),
            Will::getIt($propertyCodeTooHigh === PropertyCode::WILL ? 2 : 0),
            Intelligence::getIt($propertyCodeTooHigh === PropertyCode::INTELLIGENCE ? 2 : 0),
            Charisma::getIt($propertyCodeTooHigh === PropertyCode::CHARISMA ? 2 : 0)
        );
    }

    /**
     * @test
     */
    public function I_can_set_and_get_profession_levels()
    {
        $professionNextLevel = ProfessionNextLevel::createNextLevel(
            $this->createProfession(ProfessionCode::FIGHTER),
            $this->createLevelRank(2),
            $this->createStrength(ProfessionCode::FIGHTER),
            $this->createAgility(ProfessionCode::FIGHTER),
            $this->createKnack(ProfessionCode::FIGHTER),
            $this->createWill(ProfessionCode::FIGHTER),
            $this->createIntelligence(ProfessionCode::FIGHTER),
            $this->createCharisma(ProfessionCode::FIGHTER),
            new \DateTimeImmutable()
        );
        self::assertNull($professionNextLevel->getProfessionLevels());
        $professionNextLevel->setProfessionLevels($professionLevels = $this->createProfessionLevels());
        self::assertSame($professionLevels, $professionNextLevel->getProfessionLevels());
    }

    /**
     * @return MockInterface|ProfessionLevels
     */
    private function createProfessionLevels(): ProfessionLevels
    {
        return $this->mockery(ProfessionLevels::class);
    }
}