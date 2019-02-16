<?php
declare(strict_types = 1);

namespace DrdPlus\Person\ProfessionLevels;

use DrdPlus\Professions\Commoner;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use PHPUnit\Framework\TestCase;

class ProfessionZeroLevelTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $professionZeroLevel = ProfessionZeroLevel::createZeroLevel($commoner = Commoner::getIt());
        self::assertInstanceOf(ProfessionZeroLevel::class, $professionZeroLevel);
        self::assertSame($commoner, $professionZeroLevel->getProfession());
        self::assertInstanceOf(LevelRank::class, $levelRank = $professionZeroLevel->getLevelRank());
        self::assertSame(0, $levelRank->getValue());
        self::assertInstanceOf(Strength::class, $strengthIncrement = $professionZeroLevel->getStrengthIncrement());
        self::assertSame(0, $strengthIncrement->getValue());
        self::assertInstanceOf(Agility::class, $agilityIncrement = $professionZeroLevel->getAgilityIncrement());
        self::assertSame(0, $agilityIncrement->getValue());
        self::assertInstanceOf(Knack::class, $knackIncrement = $professionZeroLevel->getKnackIncrement());
        self::assertSame(0, $knackIncrement->getValue());
        self::assertInstanceOf(Will::class, $willIncrement = $professionZeroLevel->getWillIncrement());
        self::assertSame(0, $willIncrement->getValue());
        self::assertInstanceOf(Intelligence::class, $intelligenceIncrement = $professionZeroLevel->getIntelligenceIncrement());
        self::assertSame(0, $intelligenceIncrement->getValue());
        self::assertInstanceOf(Charisma::class, $charismaIncrement = $professionZeroLevel->getCharismaIncrement());
        self::assertSame(0, $charismaIncrement->getValue());
        self::assertGreaterThanOrEqual(time() - 1, $professionZeroLevel->getLevelUpAt()->getTimestamp());
        self::assertLessThanOrEqual(time() + 1, $professionZeroLevel->getLevelUpAt()->getTimestamp());

        $professionZeroLevel = ProfessionZeroLevel::createZeroLevel(
            Commoner::getIt(),
            $when = new \DateTimeImmutable('2016-10-09 19:06:00T+02:00')
        );
        self::assertSame($when, $professionZeroLevel->getLevelUpAt());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidZeroLevelRank
     * @expectedExceptionMessageRegExp ~[^\d]0[^\d]~
     */
    public function I_can_not_create_it_with_higher_level_thank_zero()
    {
        $professionZeroLevel = ProfessionZeroLevel::createZeroLevel($commoner = Commoner::getIt());
        $reflection = new \ReflectionClass(ProfessionZeroLevel::class);
        /** @see \DrdPlus\Person\ProfessionLevels\ProfessionLevel::__construct */
        $constructor = $reflection->getMethod('__construct');
        $constructor->setAccessible(true);
        $constructor->invokeArgs(
            $professionZeroLevel,
            [
                $commoner,
                LevelRank::getIt(1),
                Strength::getIt(0),
                Agility::getIt(0),
                Knack::getIt(0),
                Will::getIt(0),
                Intelligence::getIt(0),
                Charisma::getIt(0),
            ]
        );
    }

    /**
     * @test
     * @dataProvider provideIncrementedProperty
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidZeroLevelPropertyValue
     * @param Strength $strength
     * @param Agility $agility
     * @param Knack $knack
     * @param Will $will
     * @param Intelligence $intelligence
     * @param Charisma $charisma
     */
    public function I_can_not_create_it_with_property_increment(
        Strength $strength,
        Agility $agility,
        Knack $knack,
        Will $will,
        Intelligence $intelligence,
        Charisma $charisma
    )
    {
        $professionZeroLevel = ProfessionZeroLevel::createZeroLevel($commoner = Commoner::getIt());
        $reflection = new \ReflectionClass(ProfessionZeroLevel::class);
        /** @see \DrdPlus\Person\ProfessionLevels\ProfessionLevel::__construct */
        $constructor = $reflection->getMethod('__construct');
        $constructor->setAccessible(true);
        $constructor->invokeArgs(
            $professionZeroLevel,
            [$commoner, LevelRank::getIt(0), $strength, $agility, $knack, $will, $intelligence, $charisma]
        );
    }

    public function provideIncrementedProperty()
    {
        $incremented = [];
        $properties = [Strength::class, Agility::class, Knack::class, Will::class, Intelligence::class, Charisma::class];
        foreach ($properties as $propertyToIncrement) {
            $incrementedProperties = [];
            foreach ($properties as $property) {
                if ($property === $propertyToIncrement) {
                    $incrementedProperties[] = $property::getIt(1);
                } else {
                    $incrementedProperties[] = $property::getIt(0);
                }
            }
            $incremented[] = $incrementedProperties;
        }

        return $incremented;
    }
}