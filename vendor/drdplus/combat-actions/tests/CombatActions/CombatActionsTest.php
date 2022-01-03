<?php declare(strict_types=1);

namespace DrdPlus\Tests\CombatActions;

use DrdPlus\Codes\CombatActions\CombatActionCode;
use DrdPlus\Codes\CombatActions\MeleeCombatActionCode;
use DrdPlus\Codes\CombatActions\RangedCombatActionCode;
use DrdPlus\CombatActions\CombatActions;
use DrdPlus\Tables\Combat\Actions\CombatActionsCompatibilityTable;
use DrdPlus\Tables\Tables;
use Granam\TestWithMockery\TestWithMockery;

class CombatActionsTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it(): void
    {
        $combatActions = new CombatActions(
            $inputActions = [
                CombatActionCode::BLINDFOLD_FIGHT => CombatActionCode::getIt(CombatActionCode::BLINDFOLD_FIGHT),
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($inputActions, true /* compatible */)
        );
        self::assertCount(1, $combatActions);
        self::assertSame($inputActions, $combatActions->getCombatActionCodes());
    }

    /**
     * @param array $expectedActionsToCombine
     * @param bool $areCompatible
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithCombatActionsCompatibilityTable(array $expectedActionsToCombine, $areCompatible): Tables
    {
        $expectedActionsToCombine = array_map(
            static function ($expectedActionToCombine) {
                return (string)$expectedActionToCombine;
            },
            $expectedActionsToCombine
        );
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getCombatActionsCompatibilityTable')
            ->andReturn($combatActionsCompatibilityTable = $this->mockery(CombatActionsCompatibilityTable::class));
        $combatActionsCompatibilityTable->shouldReceive('canCombineTwoActions')
            ->with(\Mockery::type(CombatActionCode::class), \Mockery::type(CombatActionCode::class))
            ->andReturnUsing(function (CombatActionCode $someAction, CombatActionCode $anotherAction) use ($expectedActionsToCombine, $areCompatible) {
                self::assertTrue(
                    in_array($someAction->getValue(), $expectedActionsToCombine, true),
                    "Unexpected {$someAction}, expected one of " . implode(',', $expectedActionsToCombine)
                );
                self::assertTrue(
                    in_array($anotherAction->getValue(), $expectedActionsToCombine, true),
                    "Unexpected {$anotherAction}, expected one of " . implode(',', $expectedActionsToCombine)
                );

                return $areCompatible;
            });

        return $tables;
    }

    /**
     * @test
     */
    public function I_can_get_codes(): void
    {
        $combatActions = new CombatActions(
            $expected = [
                CombatActionCode::MOVE,
                CombatActionCode::ATTACK_ON_DISABLED_OPPONENT,
                CombatActionCode::BLINDFOLD_FIGHT,
                CombatActionCode::SWAP_WEAPONS,
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($expected, true)
        );
        foreach ($combatActions->getCombatActionCodes() as $combatActionCode) {
            $collected[] = $combatActionCode->getValue();
        }
        sort($expected);
        sort($collected);
        self::assertSame($expected, $collected);
    }

    /**
     * @test
     */
    public function I_can_iterate_through_them(): void
    {
        $combatActions = new CombatActions(
            $expected = [
                CombatActionCode::MOVE,
                CombatActionCode::ATTACK_ON_DISABLED_OPPONENT,
                CombatActionCode::BLINDFOLD_FIGHT,
                CombatActionCode::SWAP_WEAPONS,
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($expected, true)
        );
        $collected = [];
        foreach ($combatActions as $combatActionCode) {
            $collected[] = $combatActionCode->getValue();
        }
        sort($expected);
        sort($collected);
        self::assertSame($expected, $collected);
    }

    /**
     * @test
     */
    public function I_can_count_them(): void
    {
        $combatActions = new CombatActions([], $this->createTablesWithCombatActionsCompatibilityTable([], true));
        self::assertCount(0, $combatActions);

        $combatActions = new CombatActions(
            $values = [
                CombatActionCode::MOVE,
                CombatActionCode::ATTACK_ON_DISABLED_OPPONENT,
                CombatActionCode::BLINDFOLD_FIGHT,
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($values, true)
        );
        self::assertCount(3, $combatActions);
    }

    /**
     * @test
     */
    public function I_can_get_list_of_actions_as_string(): void
    {
        $combatActions = new CombatActions([], $this->createTablesWithCombatActionsCompatibilityTable([], true));
        self::assertSame('', (string)$combatActions);

        $combatActions = new CombatActions(
            $values = [
                CombatActionCode::ATTACK_ON_DISABLED_OPPONENT,
                CombatActionCode::BLINDFOLD_FIGHT,
                CombatActionCode::LAYING,
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($values, true)
        );
        self::assertSame(implode(',', $values), (string)$combatActions);
    }

    /**
     * @test
     */
    public function I_can_get_fight_number_modifier(): void
    {
        $combatActions = new CombatActions(
            $values = [
                CombatActionCode::ATTACK_ON_DISABLED_OPPONENT,
                CombatActionCode::BLINDFOLD_FIGHT,
                CombatActionCode::ATTACKED_FROM_BEHIND,
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($values, true)
        );
        self::assertSame(0, $combatActions->getFightNumberModifier());

        $combatActions = new CombatActions(
            $values = [
                CombatActionCode::LAYING, // -4
                CombatActionCode::SITTING_OR_ON_KNEELS, // -2
                CombatActionCode::PUTTING_ON_ARMOR, // -4
                CombatActionCode::PUTTING_ON_ARMOR_WITH_HELP, // -2
                CombatActionCode::HELPING_TO_PUT_ON_ARMOR, // -2
                CombatActionCode::CONCENTRATION_ON_DEFENSE, // +2
                RangedCombatActionCode::AIMED_SHOT, // -2
                CombatActionCode::SWAP_WEAPONS, // -2
                CombatActionCode::HANDOVER_ITEM, // -2
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($values, true)
        );
        self::assertSame(-18, $combatActions->getFightNumberModifier());
    }

    /**
     * @test
     */
    public function I_can_get_attack_number_modifier(): void
    {
        $combatActions = new CombatActions(
            $genericValues = [
                CombatActionCode::PUT_OUT_EASILY_ACCESSIBLE_ITEM, // +2
                CombatActionCode::PUT_OUT_HARDLY_ACCESSIBLE_ITEM, // +2
                CombatActionCode::LAYING, // -4
                CombatActionCode::SITTING_OR_ON_KNEELS, // -2
                CombatActionCode::BLINDFOLD_FIGHT, // -6
                CombatActionCode::FIGHT_IN_REDUCED_VISIBILITY, // -1
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($genericValues, true)
        );
        self::assertSame(-9, $combatActions->getAttackNumberModifier());

        $meleeValues = $genericValues;
        $meleeValues[] = MeleeCombatActionCode::HEADLESS_ATTACK; // +2
        $meleeValues[] = MeleeCombatActionCode::PRESSURE; // +2
        $combatActions = new CombatActions(
            $meleeValues,
            $this->createTablesWithCombatActionsCompatibilityTable($meleeValues, true)
        );
        self::assertSame(-5, $combatActions->getAttackNumberModifier());

        $rangedValues = $genericValues;
        $rangedValues[] = RangedCombatActionCode::AIMED_SHOT; // +0 (zero rounds of aiming as default value)
        $combatActions = new CombatActions(
            $rangedValues,
            $this->createTablesWithCombatActionsCompatibilityTable($rangedValues, true)
        );
        self::assertSame(-9, $combatActions->getAttackNumberModifier());

        $combatActions = new CombatActions(
            $rangedValues,
            $this->createTablesWithCombatActionsCompatibilityTable($rangedValues, true),
            2 // aiming for 2 rounds
        );
        self::assertSame(-7 /* +2 for max aiming */, $combatActions->getAttackNumberModifier());

        $combatActions = new CombatActions(
            $rangedValues,
            $this->createTablesWithCombatActionsCompatibilityTable($rangedValues, true),
            11 // aiming for 11 rounds (up to 3 rounds should be counted)
        );
        self::assertSame(-6 /* +3 for max aiming */, $combatActions->getAttackNumberModifier());
    }

    /**
     * @test
     */
    public function I_can_get_base_of_wounds_modifier(): void
    {
        $combatActions = new CombatActions(
            $values = [
                MeleeCombatActionCode::HEADLESS_ATTACK, // +2
                MeleeCombatActionCode::FLAT_ATTACK, // -6
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($values, true)
        );
        self::assertSame(-4, $combatActions->getBaseOfWoundsModifier(false /* not crushing weapon */));

        $combatActions = new CombatActions(
            $values = [
                MeleeCombatActionCode::HEADLESS_ATTACK, // +2
                MeleeCombatActionCode::FLAT_ATTACK, // 0 because of crushing weapon
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($values, true)
        );
        self::assertSame(2, $combatActions->getBaseOfWoundsModifier(true /* crushing weapon */));
    }

    /**
     * @test
     */
    public function I_can_get_defense_number_modifier(): void
    {
        $combatActions = new CombatActions(
            $values = [
                MeleeCombatActionCode::HEADLESS_ATTACK, // -5
                CombatActionCode::CONCENTRATION_ON_DEFENSE, // +2
                MeleeCombatActionCode::PRESSURE, // -1
                MeleeCombatActionCode::RETREAT, // +1
                MeleeCombatActionCode::LAYING, // -4
                MeleeCombatActionCode::SITTING_OR_ON_KNEELS, // -2
                MeleeCombatActionCode::PUTTING_ON_ARMOR, // -4
                MeleeCombatActionCode::PUTTING_ON_ARMOR_WITH_HELP, // -4
                MeleeCombatActionCode::ATTACKED_FROM_BEHIND, // -4
                MeleeCombatActionCode::BLINDFOLD_FIGHT, // -10
                MeleeCombatActionCode::FIGHT_IN_REDUCED_VISIBILITY, // -2
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($values, true)
        );
        self::assertSame(-33, $combatActions->getDefenseNumberModifier());
    }

    /**
     * @test
     */
    public function I_can_get_defense_number_modifier_against_faster_opponent()
    {
        $combatActions = new CombatActions(
            $values = [
                MeleeCombatActionCode::HEADLESS_ATTACK, // -5
                CombatActionCode::CONCENTRATION_ON_DEFENSE, // +2
                MeleeCombatActionCode::PRESSURE, // -1
                MeleeCombatActionCode::RETREAT, // +1
                MeleeCombatActionCode::LAYING, // -4
                MeleeCombatActionCode::SITTING_OR_ON_KNEELS, // -2
                MeleeCombatActionCode::PUTTING_ON_ARMOR, // -4
                MeleeCombatActionCode::PUTTING_ON_ARMOR_WITH_HELP, // -4
                MeleeCombatActionCode::ATTACKED_FROM_BEHIND, // -4
                MeleeCombatActionCode::BLINDFOLD_FIGHT, // -10
                MeleeCombatActionCode::FIGHT_IN_REDUCED_VISIBILITY, // -2
                MeleeCombatActionCode::RUN, // -4
                MeleeCombatActionCode::PUT_OUT_HARDLY_ACCESSIBLE_ITEM, // -4
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($values, true)
        );
        self::assertSame(-41, $combatActions->getDefenseNumberModifierAgainstFasterOpponent());
    }

    /**
     * @test
     */
    public function I_can_get_speed_modifier()
    {
        $combatActions = new CombatActions(
            $values = [
                CombatActionCode::MOVE, // +8
                CombatActionCode::RUN, // +22
            ],
            $this->createTablesWithCombatActionsCompatibilityTable($values, true /* just a little hack */)
        );
        self::assertSame(30, $combatActions->getSpeedModifier());
    }

    /**
     * @test
     */
    public function I_can_ask_it_if_uses_simplified_lighting_rules_by_fight_actions()
    {
        $combatActions = new CombatActions([CombatActionCode::MOVE, CombatActionCode::SWAP_WEAPONS], Tables::getIt());
        self::assertFalse($combatActions->usesSimplifiedLightingRules());

        $combatActions = new CombatActions(
            [CombatActionCode::ATTACK_ON_DISABLED_OPPONENT, CombatActionCode::BLINDFOLD_FIGHT],
            Tables::getIt()
        );
        self::assertTrue($combatActions->usesSimplifiedLightingRules());
        $combatActions = new CombatActions(
            [CombatActionCode::FIGHT_IN_REDUCED_VISIBILITY, CombatActionCode::SITTING_OR_ON_KNEELS],
            Tables::getIt()
        );
        self::assertTrue($combatActions->usesSimplifiedLightingRules());
    }

    /**
     * @test
     */
    public function I_can_not_create_it_with_unknown_code()
    {
        $this->expectException(\DrdPlus\CombatActions\Exceptions\UnknownCombatActionCode::class);
        $this->expectExceptionMessageMatches('~swimming_against_current~');
        new CombatActions(['swimming_against_current'], Tables::getIt());
    }

    /**
     * @test
     */
    public function I_can_not_combine_ranged_only_with_melee_only_actions()
    {
        $this->expectException(\DrdPlus\CombatActions\Exceptions\IncompatibleCombatActions::class);
        new CombatActions(
            [MeleeCombatActionCode::COVER_OF_ALLY, RangedCombatActionCode::AIMED_SHOT],
            Tables::getIt()
        );
    }

    /**
     * @test
     */
    public function I_can_not_combine_native_incompatible_actions()
    {
        $this->expectException(\DrdPlus\CombatActions\Exceptions\IncompatibleCombatActions::class);
        $this->expectExceptionMessageMatches("~'attack_on_disabled_opponent' with 'getting_up'~");
        new CombatActions(
            [CombatActionCode::ATTACK_ON_DISABLED_OPPONENT, CombatActionCode::GETTING_UP],
            $this->createTablesWithCombatActionsIncompatibilityTable()
        );
    }

    /**
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithCombatActionsIncompatibilityTable()
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getCombatActionsCompatibilityTable')
            ->andReturn($combatActionsIncompatibilityTable = $this->mockery(CombatActionsCompatibilityTable::class));
        $combatActionsIncompatibilityTable->shouldReceive('canCombineTwoActions')
            ->with(\Mockery::type(CombatActionCode::class), \Mockery::type(CombatActionCode::class))
            ->andReturn(false);

        return $tables;
    }

    /**
     * @test
     */
    public function I_can_not_use_non_integer_as_rounds_of_aiming()
    {
        $this->expectException(\DrdPlus\CombatActions\Exceptions\InvalidFormatOfRoundsOfAiming::class);
        $this->expectExceptionMessageMatches('~lifetime~');
        new CombatActions(
            [CombatActionCode::GETTING_UP, CombatActionCode::HANDOVER_ITEM],
            Tables::getIt(),
            'lifetime'
        );
    }
}
