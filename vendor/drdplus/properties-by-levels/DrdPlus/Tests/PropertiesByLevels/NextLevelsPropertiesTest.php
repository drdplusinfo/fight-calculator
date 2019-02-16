<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\PropertiesByLevels;

use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\PropertiesByLevels\NextLevelsProperties;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use Granam\Tests\Tools\TestWithMockery;

class NextLevelsPropertiesTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_get_properties()
    {
        $sut = new NextLevelsProperties($this->createProfessionLevels(
            $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        ));

        self::assertInstanceOf(Strength::class, $sut->getNextLevelsStrength());
        self::assertSame($strength, $sut->getNextLevelsStrength()->getValue());

        self::assertInstanceOf(Agility::class, $sut->getNextLevelsAgility());
        self::assertSame($agility, $sut->getNextLevelsAgility()->getValue());

        self::assertInstanceOf(Knack::class, $sut->getNextLevelsKnack());
        self::assertSame($knack, $sut->getNextLevelsKnack()->getValue());

        self::assertInstanceOf(Will::class, $sut->getNextLevelsWill());
        self::assertSame($will, $sut->getNextLevelsWill()->getValue());

        self::assertInstanceOf(Intelligence::class, $sut->getNextLevelsIntelligence());
        self::assertSame($intelligence, $sut->getNextLevelsIntelligence()->getValue());

        self::assertInstanceOf(Charisma::class, $sut->getNextLevelsCharisma());
        self::assertSame($charisma, $sut->getNextLevelsCharisma()->getValue());
    }

    /**
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     * @return \Mockery\MockInterface|ProfessionLevels
     */
    private function createProfessionLevels($strength, $agility, $knack, $will, $intelligence, $charisma)
    {
        $professionLevels = $this->mockery(ProfessionLevels::class);
        $professionLevels->shouldReceive('getNextLevelsStrengthModifier')
            ->andReturn($strength);
        $professionLevels->shouldReceive('getNextLevelsAgilityModifier')
            ->andReturn($agility);
        $professionLevels->shouldReceive('getNextLevelsKnackModifier')
            ->andReturn($knack);
        $professionLevels->shouldReceive('getNextLevelsWillModifier')
            ->andReturn($will);
        $professionLevels->shouldReceive('getNextLevelsIntelligenceModifier')
            ->andReturn($intelligence);
        $professionLevels->shouldReceive('getNextLevelsCharismaModifier')
            ->andReturn($charisma);

        return $professionLevels;
    }
}