<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\Effects\SeveredArmEffect;
use DrdPlus\Health\Afflictions\SpecificAfflictions\SeveredArm;

class SeveredArmEffectTest extends AfflictionEffectTest
{
    /**
     * @test
     */
    public function I_can_find_out_if_apply_even_on_success_against_trap()
    {
        $severedArmEffect = SeveredArmEffect::getIt();
        self::assertTrue($severedArmEffect->isEffectiveEvenOnSuccessAgainstTrap());
    }

    /**
     * @test
     */
    public function I_can_get_strength_and_knack_malus()
    {
        $severedArmEffect = SeveredArmEffect::getIt();
        self::assertSame(-123, $severedArmEffect->getStrengthMalus($this->createSeveredArm(123)));
        self::assertSame(-246, $severedArmEffect->getKnackMalus($this->createSeveredArm(123)));
    }

    /**
     * @param $serverArmSize
     * @return \Mockery\MockInterface|SeveredArm
     */
    private function createSeveredArm($serverArmSize)
    {
        $severedArm = $this->mockery(SeveredArm::class);
        $severedArm->shouldReceive('getAfflictionSize')
            ->andReturn(AfflictionSize::getIt($serverArmSize));

        return $severedArm;
    }

}
