<?php
namespace DrdPlus\Tests\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\Effects\ThirstEffect;

class ThirstEffectTest extends AfflictionEffectTest
{
    /**
     * @test
     */
    public function I_can_find_out_if_apply_even_on_success_against_trap()
    {
        $thirstEffect = ThirstEffect::getIt();
        self::assertTrue($thirstEffect->isEffectiveEvenOnSuccessAgainstTrap());
    }
}
