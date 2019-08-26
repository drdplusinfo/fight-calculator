<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions\Effects;

use DrdPlus\Health\Afflictions\Effects\AfflictionEffect;
use DrdPlus\Health\Afflictions\Effects\SeveredArmEffect;
use Granam\String\StringTools;
use Granam\Tests\Tools\TestWithMockery;

abstract class AfflictionEffectTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        /** @var SeveredArmEffect $sutClass */
        $sutClass = self::getSutClass();
        /** @var AfflictionEffect $effect */
        $effect = $sutClass::getIt();
        self::assertInstanceOf($sutClass, $effect);
        self::assertSame(
            $effect,
            $sameEffect = $sutClass::getEnum($this->getEffectCode()),
            "Expected {$effect} to be the very same instance as {$sameEffect}"
        );
        self::assertInstanceOf($sutClass, $effect);
        self::assertSame($this->getEffectCode(), $effect->getValue());
    }

    private function getEffectCode()
    {
        return StringTools::camelCaseToSnakeCasedBasename(self::getSutClass());
    }

    /**
     * @test
     */
    abstract public function I_can_find_out_if_apply_even_on_success_against_trap();
}