<?php
namespace DrdPlus\Tests\Health;

use DrdPlus\Health\MalusFromWounds;
use PHPUnit\Framework\TestCase;

class MalusFromWoundsTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $malusFromWounds = MalusFromWounds::getIt(-2);
        self::assertInstanceOf(MalusFromWounds::class, $malusFromWounds);
        self::assertSame(-2, $malusFromWounds->getValue());

        $malusFromWounds = MalusFromWounds::getIt(0);
        self::assertSame(0, $malusFromWounds->getValue());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\UnexpectedMalusValue
     * @expectedExceptionMessageRegExp ~1~
     */
    public function I_can_not_create_positive_malus()
    {
        MalusFromWounds::getIt(1);
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Exceptions\UnexpectedMalusValue
     * @expectedExceptionMessageRegExp ~-4~
     */
    public function I_can_not_create_worse_malus_than_minus_three()
    {
        MalusFromWounds::getIt(-4);
    }
}