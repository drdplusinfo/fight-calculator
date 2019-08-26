<?php declare(strict_types=1);

namespace DrdPlus\Tests\FightProperties;

use DrdPlus\FightProperties\BodyPropertiesForFight;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Properties\Derived\Speed;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class BodyPropertiesForFightTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $bodyPropertiesForFight = new BodyPropertiesForFight(
            $strength = Strength::getIt(123),
            $agility = Agility::getIt(234),
            $knack = Knack::getIt(345),
            $will = Will::getIt(456),
            $intelligence = Intelligence::getIt(567),
            $charisma = Charisma::getIt(789),
            $size = Size::getIt(890),
            $height = Height::getIt(HeightInCm::getIt(987), Tables::getIt()),
            $speed = Speed::getIt($strength, $agility, $height)
        );
        self::assertSame($strength, $bodyPropertiesForFight->getStrength());
        self::assertSame($strength, $bodyPropertiesForFight->getStrengthOfMainHand());
        $strengthOfOffhand = $bodyPropertiesForFight->getStrengthOfOffhand();
        self::assertInstanceOf(Strength::class, $strengthOfOffhand);
        self::assertSame(121, $strengthOfOffhand->getValue());
        self::assertSame($agility, $bodyPropertiesForFight->getAgility());
        self::assertSame($knack, $bodyPropertiesForFight->getKnack());
        self::assertSame($will, $bodyPropertiesForFight->getWill());
        self::assertSame($intelligence, $bodyPropertiesForFight->getIntelligence());
        self::assertSame($charisma, $bodyPropertiesForFight->getCharisma());
        self::assertSame($size, $bodyPropertiesForFight->getSize());
        self::assertSame($height, $bodyPropertiesForFight->getHeight());
        self::assertSame($speed, $bodyPropertiesForFight->getSpeed());
    }
}