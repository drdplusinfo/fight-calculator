<?php declare(strict_types=1);

namespace Tests\DrdPlus\AttackSkeleton\InjectorComposerPlugin;

use DrdPlus\AttackSkeleton\InjectorComposerPlugin\AttackInjectorComposerPlugin;
use Tests\DrdPlus\AttackSkeleton\Partials\AttackCalculatorTestTrait;
use Tests\DrdPlus\CalculatorSkeleton\Partials\AbstractCalculatorContentTest;

class AttackInjectorComposerPluginTest extends AbstractCalculatorContentTest
{
    use AttackCalculatorTestTrait;

    /**
     * @test
     */
    public function I_can_create_it()
    {
        self::assertNotNull(new AttackInjectorComposerPlugin());
    }

    /**
     * @test
     */
    public function Name_of_package_matches(): void
    {
        if (!$this->isAttackSkeletonChecked()) {
            self::assertTrue(true, 'Should be fine');

            return;
        }
        self::assertSame(AttackInjectorComposerPlugin::ATTACK_SKELETON_PACKAGE_NAME, $this->getComposerConfig()['name']);
    }

    /**
     * @test
     */
    public function Package_is_injected(): void
    {
        if (!$this->isAttackSkeletonChecked()) {
            self::assertFalse(false, 'Intended for skeleton only');

            return;
        }
        self::assertSame('composer-plugin', $this->getComposerConfig()['type']);
        self::assertSame(AttackInjectorComposerPlugin::class, $this->getComposerConfig()['extra']['class']);
        self::assertArrayHasKey('composer-plugin-api', $this->getComposerConfig()['require']);
    }
}
