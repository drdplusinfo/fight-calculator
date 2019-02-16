<?php
namespace DrdPlus\Tests\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\Effects\ColdEffect;
use DrdPlus\Health\Afflictions\SpecificAfflictions\Cold;

class ColdEffectTest extends AfflictionEffectTest
{

    /**
     * @test
     */
    public function I_can_get_strength_agility_and_knack_malus()
    {
        $coldEffect = ColdEffect::getIt();

        self::assertSame(0, $coldEffect->getStrengthMalus($this->createCold(0)));
        self::assertSame(-1, $coldEffect->getStrengthMalus($this->createCold(1)));
        self::assertSame(-1, $coldEffect->getStrengthMalus($this->createCold(4)));
        self::assertSame(-3, $coldEffect->getStrengthMalus($this->createCold(11)));
        self::assertSame(-4, $coldEffect->getStrengthMalus($this->createCold(13)));

        self::assertSame(0, $coldEffect->getAgilityMalus($this->createCold(0)));
        self::assertSame(-1, $coldEffect->getAgilityMalus($this->createCold(1)));
        self::assertSame(-1, $coldEffect->getAgilityMalus($this->createCold(4)));
        self::assertSame(-3, $coldEffect->getAgilityMalus($this->createCold(11)));
        self::assertSame(-4, $coldEffect->getAgilityMalus($this->createCold(13)));

        self::assertSame(0, $coldEffect->getKnackMalus($this->createCold(0)));
        self::assertSame(-1, $coldEffect->getKnackMalus($this->createCold(1)));
        self::assertSame(-1, $coldEffect->getKnackMalus($this->createCold(4)));
        self::assertSame(-3, $coldEffect->getKnackMalus($this->createCold(11)));
        self::assertSame(-4, $coldEffect->getKnackMalus($this->createCold(13)));
    }

    /**
     * @param int $coldSize
     * @return \Mockery\MockInterface|Cold
     */
    private function createCold($coldSize)
    {
        $cold = $this->mockery(Cold::class);
        $cold->shouldReceive('getAfflictionSize')
            ->andReturn(AfflictionSize::getIt($coldSize));

        return $cold;
    }

    /**
     * @test
     */
    public function I_can_find_out_if_apply_even_on_success_against_trap()
    {
        $coldEffect = ColdEffect::getIt();

        self::assertFalse($coldEffect->isEffectiveEvenOnSuccessAgainstTrap());
    }
}
