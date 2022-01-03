<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions;

use DrdPlus\Health\Afflictions\AfflictionVirulence;
use PHPUnit\Framework\TestCase;

class AfflictionVirulenceTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_get_every_virulence()
    {
        $roundVirulence = AfflictionVirulence::getRoundVirulence();
        self::assertInstanceOf(AfflictionVirulence::class, $roundVirulence);
        self::assertSame('round', $roundVirulence->getValue());

        $minuteVirulence = AfflictionVirulence::getMinuteVirulence();
        self::assertInstanceOf(AfflictionVirulence::class, $minuteVirulence);
        self::assertSame('minute', $minuteVirulence->getValue());

        $hourVirulence = AfflictionVirulence::getHourVirulence();
        self::assertInstanceOf(AfflictionVirulence::class, $hourVirulence);
        self::assertSame('hour', $hourVirulence->getValue());

        $dayVirulence = AfflictionVirulence::getDayVirulence();
        self::assertInstanceOf(AfflictionVirulence::class, $dayVirulence);
        self::assertSame('day', $dayVirulence->getValue());
    }

    /**
     * @test
     */
    public function I_can_not_create_custom_virulence()
    {
        $this->expectException(\DrdPlus\Health\Afflictions\Exceptions\UnknownVirulencePeriod::class);
        $this->expectExceptionMessageMatches('~life~');
        AfflictionVirulence::getEnum('life');
    }
}
