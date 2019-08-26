<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\Effects\PainEffect;
use DrdPlus\Health\Afflictions\SpecificAfflictions\Pain;

class PainEffectTest extends AfflictionEffectTest
{
    /**
     * @test
     */
    public function I_can_find_out_if_apply_even_on_success_against_trap()
    {
        $painEffect = PainEffect::getIt();
        self::assertFalse($painEffect->isEffectiveEvenOnSuccessAgainstTrap());
    }

    /**
     * @test
     */
    public function I_can_get_malus_from_pain()
    {
        $painEffect = PainEffect::getIt();
        $pain = $this->mockery(Pain::class);
        $pain->shouldReceive('getAfflictionSize')
            ->andReturn(AfflictionSize::getIt(123));
        /** @var Pain $pain */
        self::assertSame(-123, $painEffect->getMalusFromPain($pain));
    }

}
