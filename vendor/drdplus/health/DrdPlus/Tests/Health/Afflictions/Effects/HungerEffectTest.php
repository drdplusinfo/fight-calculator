<?php
namespace DrdPlus\Tests\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\Effects\HungerEffect;

class HungerEffectTest extends AfflictionEffectTest
{
    /**
     * @test
     */
    public function I_can_find_out_if_apply_even_on_success_against_trap()
    {
        $hungerEffect = HungerEffect::getIt();
        self::assertTrue($hungerEffect->isEffectiveEvenOnSuccessAgainstTrap());
    }
}
