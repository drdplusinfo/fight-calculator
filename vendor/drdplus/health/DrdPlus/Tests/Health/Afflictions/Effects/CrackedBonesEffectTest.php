<?php
namespace DrdPlus\Tests\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\Effects\CrackedBonesEffect;
use DrdPlus\Health\Afflictions\SpecificAfflictions\CrackedBones;

class CrackedBonesEffectTest extends AfflictionEffectTest
{
    /**
     * @test
     */
    public function I_can_get_healing_malus()
    {
        $crackedBonesEffect = CrackedBonesEffect::getIt();
        /** @var CrackedBones|\Mockery\MockInterface $crackedBones */
        $crackedBones = $this->mockery(CrackedBones::class);
        $crackedBones->shouldReceive('getAfflictionSize')
            ->andReturn(AfflictionSize::getIt(123));
        self::assertSame(-123, $crackedBonesEffect->getHealingMalus($crackedBones));
    }

    /**
     * @test
     */
    public function I_can_find_out_if_apply_even_on_success_against_trap()
    {
        $crackedBonesEffect = CrackedBonesEffect::getIt();
        self::assertTrue($crackedBonesEffect->isEffectiveEvenOnSuccessAgainstTrap());
    }

}
