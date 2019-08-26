<?php declare(strict_types=1);

namespace DrdPlus\Tests\FightProperties;

use DrdPlus\Codes\Armaments\ArmamentCode;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\Units\DistanceUnitCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Body\PhysicalWoundTypeCode;
use DrdPlus\CombatActions\CombatActions;
use DrdPlus\FightProperties\BodyPropertiesForFight;
use DrdPlus\FightProperties\FightProperties;
use DrdPlus\Health\Inflictions\Glared;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Properties\Combat\Attack;
use DrdPlus\Properties\Combat\AttackNumber;
use DrdPlus\Properties\Combat\Defense;
use DrdPlus\Properties\Combat\DefenseNumber;
use DrdPlus\Properties\Combat\EncounterRange;
use DrdPlus\Properties\Combat\Fight;
use DrdPlus\Properties\Combat\FightNumber;
use DrdPlus\Properties\Combat\LoadingInRounds;
use DrdPlus\Properties\Combat\MaximalRange;
use DrdPlus\Properties\Combat\Shooting;
use DrdPlus\Properties\Derived\Speed;
use DrdPlus\Skills\Skills;
use DrdPlus\Tables\Armaments\Partials\MeleeWeaponlikesTable;
use DrdPlus\Tables\Combat\Actions\CombatActionsWithWeaponTypeCompatibilityTable;
use DrdPlus\Armourer\Armourer;
use DrdPlus\Tables\Armaments\Partials\WeaponlikeTable;
use DrdPlus\Tables\Armaments\Shields\ShieldUsageSkillTable;
use DrdPlus\Tables\Armaments\Weapons\MissingWeaponSkillTable;
use DrdPlus\Tables\Body\CorrectionByHeightTable;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Measurements\Distance\DistanceBonus;
use DrdPlus\Tables\Measurements\Distance\DistanceTable;
use DrdPlus\Tables\Measurements\Wounds\WoundsBonus;
use DrdPlus\Tables\Tables;
use Granam\String\StringTools;
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class FightPropertiesTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideUsageCombinations
     * @param bool $enemyIsFasterThanYou
     * @param bool $weaponIsTwoHandedOnly
     * @param bool $holdWeaponByTwoHands
     * @param bool $weaponIsInMainHand
     * @param bool $weaponIsShield
     * @param bool $weaponIsShooting and ranged
     * @param bool $weaponIsLongerOrSameAsShield
     * @param int $targetDistanceInMeters
     * @param int $combatActionsSpeedModifier
     * @param bool $usesSimplifiedLightingRules
     * @param int $currentMalusFromLightingContrast
     * @param int $malusFromRiding
     * @param bool $onHorseback
     * @param bool $fightsAnimal
     */
    public function I_can_use_it(
        bool $enemyIsFasterThanYou,
        bool $weaponIsTwoHandedOnly,
        bool $holdWeaponByTwoHands,
        bool $weaponIsInMainHand,
        bool $weaponIsShield,
        bool $weaponIsShooting, // and implicitly ranged
        bool $weaponIsLongerOrSameAsShield,
        int $targetDistanceInMeters,
        int $combatActionsSpeedModifier,
        bool $usesSimplifiedLightingRules,
        int $currentMalusFromLightingContrast,
        int $malusFromRiding,
        bool $onHorseback,
        bool $fightsAnimal
    ): void
    {
        $armourer = $this->createArmourer();

        $weaponlikeCode = $weaponIsShield
            ? ShieldCode::getIt(ShieldCode::PAVISE)
            : $this->createWeapon(MeleeWeaponCode::CUDGEL, $weaponIsShooting);
        $strengthOfMainHand = Strength::getIt(987);
        $strengthOfOffhand = Strength::getIt(654);
        $baseStrengthForWeapon = $weaponIsInMainHand || $holdWeaponByTwoHands
            ? $strengthOfMainHand
            : $strengthOfOffhand;
        $strengthForWeapon = $holdWeaponByTwoHands && !$weaponIsTwoHandedOnly
            ? $baseStrengthForWeapon->add(2)
            : $baseStrengthForWeapon;
        $size = Size::getIt(456);
        $this->addCanUseArmament($armourer, $weaponlikeCode, $strengthForWeapon, $size, true, true, true, $weaponIsTwoHandedOnly);

        $shieldCode = $holdWeaponByTwoHands
            ? ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD)
            : ShieldCode::getIt(ShieldCode::HEAVY_SHIELD);
        // weapon if two hands means strength for main hand for a shield (if without shield, otherwise throws exception)
        $strengthForShield = $weaponIsInMainHand && !$holdWeaponByTwoHands
            ? $strengthOfOffhand
            : $strengthOfMainHand;
        $this->addCanUseArmament($armourer, $shieldCode, $strengthForShield, $size);

        $strength = Strength::getIt(987);
        $bodyPropertiesForFight = $this->createBodyPropertiesForFight(
            $strength,
            $size,
            $strengthOfMainHand,
            $strengthOfOffhand,
            $speed = $this->createSpeed(4913)
        );
        $this->addAgility($bodyPropertiesForFight, Agility::getIt(321));
        $this->addKnack($bodyPropertiesForFight, Knack::getIt(7193));
        $this->addHeight($bodyPropertiesForFight, $this->createHeight(255));

        $wornBodyArmor = BodyArmorCode::getIt(BodyArmorCode::HOBNAILED_ARMOR);
        $this->addCanUseArmament($armourer, $wornBodyArmor, $strength, $size);
        $wornHelm = HelmCode::getIt(HelmCode::WITHOUT_HELM);
        $this->addCanUseArmament($armourer, $wornHelm, $strength, $size);
        $skills = $this->createSkills();
        $missingWeaponSkillsTable = new MissingWeaponSkillTable();
        $combatActions = $this->createCombatActions($combatActionValues = ['foo'], $usesSimplifiedLightingRules);

        // attack number
        $this->addAttackNumberMalusByStrengthWithWeaponlike(
            $armourer,
            $weaponlikeCode,
            $strengthForWeapon,
            $attackNumberMalusByStrengthWithWeapon = 442271
        );
        $this->addMalusToAttackNumberFromSkillsWithWeaponlike(
            $skills,
            $weaponlikeCode,
            $missingWeaponSkillsTable,
            false, // does not fight with two weapons
            $attackNumberMalusBySkillsWithWeapon = 3450
        );
        $this->addOffensiveness($armourer, $weaponlikeCode, $offensiveness = 12123);
        $this->addCombatActionsAttackNumber($combatActions, $combatActionsAttackNumberModifier = 8171);
        $this->addMalusToAttackNumberByRiding($skills, $malusFromRiding);

        // base of wounds
        $this->addWeaponBaseOfWounds($armourer, $weaponlikeCode, $strengthForWeapon, $weaponBaseOfWounds = 91967);
        $this->addBaseOfWoundsMalusFromSkills(
            $skills,
            $weaponlikeCode,
            $missingWeaponSkillsTable,
            false, // does not fight with two weapons
            $baseOfWoundsMalusFromSkills = -12607
        );
        if ($holdWeaponByTwoHands) {
            $weaponHolding = ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        } elseif ($weaponIsInMainHand) {
            $weaponHolding = ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        } else {
            $weaponHolding = ItemHoldingCode::getIt(ItemHoldingCode::OFFHAND);
        }
        $this->addBaseOfWoundsBonusByHolding($armourer, $weaponlikeCode, $weaponHolding, $baseOfWoundsBonusForHolding = 748);
        $missingShieldSkillsTable = new ShieldUsageSkillTable();
        $tables = $this->createTables($weaponlikeCode, $combatActionValues, $missingWeaponSkillsTable, $missingShieldSkillsTable);
        $this->addWoundsTypeOf($tables, $weaponlikeCode, PhysicalWoundTypeCode::CUT);
        $this->addBaseOfWoundsModifierFromActions($combatActions, false /* weapon is not crushing */, $baseOfWoundsModifierFromActions = -1357);

        // fight number
        $fightsWithTwoWeapons = false;
        $this->addMalusToFightNumberWithWeaponlike(
            $skills,
            $weaponlikeCode,
            $missingWeaponSkillsTable,
            $fightsWithTwoWeapons,
            $fightNumberMalusFromWeapon = 44
        );
        $this->addFightNumberMalusByStrengthWithWeaponOrShield(
            $armourer,
            $weaponlikeCode,
            $strengthForWeapon,
            $fightNumberMalusByStrengthWithWeapon = 45
        );
        $this->addFightNumberMalusByStrengthWithWeaponOrShield(
            $armourer,
            $shieldCode,
            $strengthForShield,
            $fightNumberMalusByStrengthWithShield = 56
        );
        $this->addFightNumberMalusFromProtectivesBySkills(
            $skills,
            $armourer,
            $wornBodyArmor,
            $fightNumberMalusFromBodyArmor = 11,
            $wornHelm,
            $fightNumberMalusFromHelm = 22,
            $shieldCode,
            $fightNumberMalusFromShield = 33,
            $weaponIsShield ? $weaponlikeCode : null,
            $fightNumberMalusFromShieldAsWeapon = $weaponIsShield ? -4518 : 0
        );
        $this->addMalusToFightNumberByRiding($skills, $malusFromRiding);
        $weaponLength = 55;
        $shieldLength = $weaponIsLongerOrSameAsShield ? $weaponLength : 66;
        $this->addFightNumberBonusByWeaponlikeLength($tables, $armourer, $weaponlikeCode, $weaponLength, $shieldCode, $shieldLength);
        $this->addCombatActionsFightNumber($combatActions, $combatActionsFightNumberModifier = 777);

        // encounter range
        $this->addEncounterRange($armourer, $weaponlikeCode, $strengthForWeapon, $speed, $encounterRangeValue = 1824);

        // defense number
        $this->addDefenseNumberFromActions($combatActions, $enemyIsFasterThanYou, $defenseNumberModifierFromActions = -155157);
        $this->addDefenseNumberMalusByStrength($armourer, $weaponlikeCode, $strengthForWeapon, $defenseNumberMalusByStrengthWithWeapon = -518415);
        $this->addCoverOf($armourer, $weaponlikeCode, $coverOfWeapon = 6511);
        $this->addSkillsMalusToCoverWithShield(
            $skills,
            $missingShieldSkillsTable,
            $skillsMalusToCoverWithShield = -71810482
        );
        if ($weaponIsShield) {
            $skillsMalusToCoverWithWeapon = $skillsMalusToCoverWithShield;
        } else {
            $this->addSkillsMalusToCoverWithWeapon(
                $skills,
                $weaponlikeCode,
                $missingWeaponSkillsTable,
                $fightsWithTwoWeapons,
                $skillsMalusToCoverWithWeapon = -551514
            );
        }
        $this->addDefenseNumberMalusByStrength($armourer, $shieldCode, $strengthForShield, $defenseNumberMalusByStrengthWithShield = -1640);
        $this->addCoverOf($armourer, $shieldCode, $coverOfShield = 712479);
        $this->addMalusToDefenseNumberByRiding($skills, $malusFromRiding);

        // loading in rounds
        $expectedLoadingInRounds = 0;
        if ($weaponIsShooting) {
            $this->addLoadingInRoundsByStrengthWithRangedWeapon($armourer, $weaponlikeCode, $strengthForWeapon, $expectedLoadingInRounds = 56186);
        }

        // moved distance
        $this->addActionsSpeedModifier($combatActions, $combatActionsSpeedModifier);
        if ($combatActionsSpeedModifier !== 0) {
            $this->addDistanceTable($tables, $speed, $combatActionsSpeedModifier, $expectedMovedDistance = $this->createDistance(123456));
        } else {
            $expectedMovedDistance = $this->createDistance(0.0);
        }

        $this->addStrengthForWeaponOrShield(
            $armourer,
            $weaponlikeCode,
            $bodyPropertiesForFight->getStrengthOfMainHand(),
            $weaponHolding,
            $strengthForWeapon
        );
        $this->addStrengthForWeaponOrShield(
            $armourer,
            $shieldCode,
            $bodyPropertiesForFight->getStrengthOfMainHand(),
            $this->getShieldHolding($weaponHolding),
            $strengthForShield
        );

        $fightProperties = new FightProperties(
            $bodyPropertiesForFight,
            $combatActions,
            $skills,
            $wornBodyArmor,
            $wornHelm,
            $professionCode = ProfessionCode::getIt(ProfessionCode::FIGHTER),
            $tables,
            $armourer,
            $weaponlikeCode,
            $weaponHolding,
            $fightsWithTwoWeapons,
            $shieldCode,
            $enemyIsFasterThanYou,
            $this->createGlared($currentMalusFromLightingContrast),
            $onHorseback,
            $fightsAnimal
        );

        $this->I_can_get_expected_fight_and_fight_number(
            $fightProperties,
            $tables,
            $armourer,
            $professionCode,
            $bodyPropertiesForFight,
            $fightNumberMalusByStrengthWithWeapon,
            $fightNumberMalusByStrengthWithShield,
            $fightNumberMalusFromWeapon,
            $fightNumberMalusFromBodyArmor,
            $fightNumberMalusFromHelm,
            $fightNumberMalusFromShield,
            $fightNumberMalusFromShieldAsWeapon, // conditioned - mostly zero
            $weaponlikeCode,
            $weaponLength,
            $shieldCode,
            $shieldLength,
            $combatActionsFightNumberModifier,
            $onHorseback ? $malusFromRiding : 0
        );

        $targetDistance = new Distance($targetDistanceInMeters, DistanceUnitCode::METER, new DistanceTable());
        $attackNumberModifierByTargetDistance = 0;
        if ($weaponIsShooting) {
            $this->addAttackNumberModifierByDistance(
                $targetDistance,
                $armourer,
                $fightProperties->getEncounterRange(),
                $fightProperties->getMaximalRange(),
                $attackNumberModifierByTargetDistance = -8218
            );
        }
        $targetSize = Size::getIt(123);
        $attackNumberModifierByTargetSize = 0;
        if ($weaponIsShooting) {
            $this->addAttackNumberModifierBySize(
                $targetSize,
                $armourer,
                $attackNumberModifierByTargetSize = 4053
            );
        }
        $attackNumberBonusFromZoology = 0;
        if ($fightsAnimal) {
            $this->addAttackNumberBonusByZoologySkill($skills, $attackNumberBonusFromZoology = 94631);
        }
        $this->I_can_get_expected_shooting_attack_and_attack_number(
            $fightProperties,
            $bodyPropertiesForFight,
            $weaponIsShooting,
            $attackNumberMalusByStrengthWithWeapon,
            $attackNumberMalusBySkillsWithWeapon,
            $offensiveness,
            $combatActionsAttackNumberModifier,
            $targetDistance,
            $attackNumberModifierByTargetDistance,
            $targetSize,
            $attackNumberModifierByTargetSize,
            $usesSimplifiedLightingRules,
            $currentMalusFromLightingContrast,
            $attackNumberBonusFromZoology,
            $onHorseback ? $malusFromRiding : 0
        );

        $baseOfWoundsBonusFromZoology = 0;
        if ($fightsAnimal) {
            $this->addBaseOfWoundsBonusByZoologySkill($skills, $baseOfWoundsBonusFromZoology = 1937);
        }
        $this->I_can_get_expected_base_of_wounds(
            $fightProperties,
            $weaponBaseOfWounds,
            $baseOfWoundsMalusFromSkills,
            $baseOfWoundsBonusForHolding,
            $baseOfWoundsModifierFromActions,
            $baseOfWoundsBonusFromZoology
        );

        $this->I_can_get_expected_loading_in_rounds($fightProperties, $expectedLoadingInRounds);

        $this->I_can_get_expected_encounter_range($fightProperties, $encounterRangeValue);

        $this->I_can_get_expected_maximal_range($fightProperties, $weaponIsShooting);

        $coverBonusFromZoology = 0;
        if ($fightsAnimal) {
            $this->addCoverBonusByZoologySkill($skills, $coverBonusFromZoology = 784512);
        }
        $this->I_can_get_defense_and_defense_number(
            $fightProperties,
            $bodyPropertiesForFight,
            $defenseNumberModifierFromActions,
            $usesSimplifiedLightingRules,
            $currentMalusFromLightingContrast,
            $defenseNumberMalusByStrengthWithWeapon,
            $coverOfWeapon,
            $skillsMalusToCoverWithWeapon,
            $defenseNumberMalusByStrengthWithShield,
            $coverOfShield,
            $skillsMalusToCoverWithShield,
            $coverBonusFromZoology,
            $onHorseback ? $malusFromRiding : 0
        );

        $this->I_can_get_moved_distance($fightProperties, $expectedMovedDistance);
    }

    private function getShieldHolding(ItemHoldingCode $weaponHolding): ItemHoldingCode
    {
        return !$weaponHolding->holdsByTwoHands()
            ? $weaponHolding->getOpposite()
            : ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
    }

    public function provideUsageCombinations(): array
    {
        // enemy is faster than you, weapon is two handed only, holds weapon by two hands, weapon in main hand,
        // weapon is shield, weapon is shooting, weapon is longer or same as shield, target distance in meters,
        // speed modifier from combat actions, uses simplified lighting rules, malus from lighting contrast,
        // malus from riding, on horseback, fights animal
        return [
            [true, false, false, true, false, false, true, 0, 0, true, 0, -5, false, false],
            [true, false, false, true, false, false, false, 0, 0, true, 0, -5, true, true],
            [true, false /* not shooting */, false, false, true, false, true, 14789 /* distance should be ignored for non-ranged weapon */, 4596, false, 0, 0, false, false],
            [false, false, true, true, false, true, false, 1, -741, true, -987654 /* malus from lighting should be ignored on simplified rules usage */, -1, true, false],
            [false, false, true, true, false, true, true, 78515, 0, false, -123 /* odd */, -11, false, false],
            [false, true, true, false, true, false, false, 0, 1, false, -2 /* even */, -11, true, true],
        ];
    }

    /**
     * @param FightProperties $fightProperties
     * @param Tables|MockInterface $tables
     * @param Armourer|MockInterface $armourer
     * @param ProfessionCode $professionCode
     * @param BodyPropertiesForFight $bodyPropertiesForFight
     * @param int $fightNumberMalusFromStrengthForWeapon
     * @param int $fightNumberMalusFromStrengthForShield
     * @param int $fightNumberMalusFromWeapon
     * @param int $fightNumberMalusFromBodyArmor
     * @param int $fightNumberMalusFromHelm
     * @param int $fightNumberMalusFromShield
     * @param int $fightNumberMalusFromShieldAsWeapon
     * @param WeaponlikeCode $weaponlikeCode
     * @param int $weaponlikeLength
     * @param ShieldCode $shieldCode
     * @param int $shieldLength
     * @param int $combatActionsFightNumberModifier
     * @param int $ridingMalusToFightNumber
     */
    private function I_can_get_expected_fight_and_fight_number(
        FightProperties $fightProperties,
        Tables $tables,
        Armourer $armourer,
        ProfessionCode $professionCode,
        BodyPropertiesForFight $bodyPropertiesForFight,
        int $fightNumberMalusFromStrengthForWeapon,
        int $fightNumberMalusFromStrengthForShield,
        int $fightNumberMalusFromWeapon,
        int $fightNumberMalusFromBodyArmor,
        int $fightNumberMalusFromHelm,
        int $fightNumberMalusFromShield,
        int $fightNumberMalusFromShieldAsWeapon, // this is conditioned - mostly zero
        WeaponlikeCode $weaponlikeCode,
        int $weaponlikeLength,
        ShieldCode $shieldCode,
        int $shieldLength,
        int $combatActionsFightNumberModifier,
        int $ridingMalusToFightNumber
    ): void
    {
        $this->addCorrectionByHeightTable(
            $tables,
            $armourer,
            $bodyPropertiesForFight->getHeight(),
            -876,
            $weaponlikeCode,
            $weaponlikeLength,
            $shieldCode,
            $shieldLength
        );
        $fight = $fightProperties->getFight();
        self::assertSame($fight, $fightProperties->getFight(), 'Expected same instances');
        $expectedFight = Fight::getIt($professionCode, $bodyPropertiesForFight, $bodyPropertiesForFight->getHeight(), $tables);
        self::assertSame($expectedFight->getValue(), $fight->getValue(), __FUNCTION__ . ' expected different fight value');

        $fightNumber = $fightProperties->getFightNumber();
        self::assertSame($fightNumber, $fightProperties->getFightNumber(), 'Expected same instances');
        $expectedFightNumber = FightNumber::getIt($expectedFight, $shieldLength > $weaponlikeLength ? $shieldCode : $weaponlikeCode, $tables)
            ->add( // fight number modifier
                $fightNumberMalusFromStrengthForWeapon
                + $fightNumberMalusFromStrengthForShield
                + $fightNumberMalusFromWeapon
                + $fightNumberMalusFromBodyArmor
                + $fightNumberMalusFromHelm
                + $fightNumberMalusFromShield
                + $fightNumberMalusFromShieldAsWeapon // this is conditioned - mostly zero
                + $combatActionsFightNumberModifier
                + $ridingMalusToFightNumber
            );
        self::assertSame($expectedFightNumber->getValue(), $fightNumber->getValue(), __FUNCTION__ . ' expected different value');
    }

    /**
     * @param Tables|MockInterface $tables
     * @param Armourer|MockInterface $armourer
     * @param Height $expectedHeight
     * @param $correctionByHeight
     * @param WeaponlikeCode $weaponlikeCode
     * @param $weaponlikeLength = null
     * @param ShieldCode $shieldCode = null
     * @param $shieldLength = null
     * @return \Mockery\MockInterface|Tables
     */
    private function addCorrectionByHeightTable(
        Tables $tables,
        Armourer $armourer,
        Height $expectedHeight,
        int $correctionByHeight,
        WeaponlikeCode $weaponlikeCode = null,
        int $weaponlikeLength = null,
        ShieldCode $shieldCode = null,
        int $shieldLength = null
    )
    {
        $tables->shouldReceive('getCorrectionByHeightTable')
            ->andReturn($correctionByHeightTable = $this->mockery(CorrectionByHeightTable::class));
        $correctionByHeightTable->shouldReceive('getCorrectionByHeight')
            ->atLeast()->once()
            ->with($expectedHeight)
            ->andReturn($correctionByHeight);
        if ($weaponlikeCode) {
            $armourer->shouldReceive('getLengthOfWeaponOrShield')
                ->zeroOrMoreTimes()
                ->with($weaponlikeCode)
                ->andReturn($weaponlikeLength);
        }
        if ($shieldCode) {
            $armourer->shouldReceive('getLengthOfWeaponOrShield')
                ->zeroOrMoreTimes()
                ->with($shieldCode)
                ->andReturn($shieldLength);
        }

        return $tables;
    }

    /**
     * @param FightProperties $fightProperties
     * @param BodyPropertiesForFight $bodyPropertiesForFight
     * @param bool $weaponIsRanged
     * @param int $attackNumberMalusByStrengthWithWeapon
     * @param int $attackNumberMalusBySkillsWithWeapon
     * @param int $offensiveness
     * @param int $combatActionsAttackNumberModifier
     * @param Distance $targetDistance
     * @param int $attackNumberModifierByTargetDistance
     * @param Size $targetSize
     * @param int $attackNumberModifierByTargetSize
     * @param bool $usesSimplifiedLightingRules
     * @param int $currentMalusFromLightingContrast
     * @param int $attackNumberBonusFromZoology
     * @param int $malusFromRiding
     */
    private function I_can_get_expected_shooting_attack_and_attack_number(
        FightProperties $fightProperties,
        BodyPropertiesForFight $bodyPropertiesForFight,
        $weaponIsRanged,
        $attackNumberMalusByStrengthWithWeapon,
        $attackNumberMalusBySkillsWithWeapon,
        $offensiveness,
        $combatActionsAttackNumberModifier,
        Distance $targetDistance,
        $attackNumberModifierByTargetDistance,
        Size $targetSize,
        $attackNumberModifierByTargetSize,
        $usesSimplifiedLightingRules,
        $currentMalusFromLightingContrast,
        int $attackNumberBonusFromZoology,
        int $malusFromRiding
    )
    {
        $shooting = $fightProperties->getShooting();
        self::assertInstanceOf(Shooting::class, $shooting);
        $expectedShooting = Shooting::getIt($bodyPropertiesForFight->getKnack());
        self::assertSame($expectedShooting->getValue(), $shooting->getValue());

        $attack = $fightProperties->getAttack();
        self::assertInstanceOf(Attack::class, $attack);
        $expectedAttack = Attack::getIt($bodyPropertiesForFight->getAgility());
        self::assertSame($expectedAttack->getValue(), $attack->getValue());

        $attackNumber = $fightProperties->getAttackNumber($targetDistance, $targetSize);
        self::assertInstanceOf(AttackNumber::class, $attackNumber);

        $expectedBaseAttackNumber = $weaponIsRanged
            ? AttackNumber::getItFromShooting(Shooting::getIt($bodyPropertiesForFight->getKnack()))
            : AttackNumber::getItFromAttack(Attack::getIt($bodyPropertiesForFight->getAgility()));
        $expectedAttackNumber = $expectedBaseAttackNumber->add(
            $attackNumberMalusByStrengthWithWeapon
            + $attackNumberMalusBySkillsWithWeapon
            + $offensiveness
            + $combatActionsAttackNumberModifier
            + $attackNumberModifierByTargetDistance
            + $attackNumberModifierByTargetSize
            + ($usesSimplifiedLightingRules ? 0 : round($currentMalusFromLightingContrast / 2 /* just half */))
            + $attackNumberBonusFromZoology
            + $malusFromRiding
        );
        self::assertSame(
            $expectedAttackNumber->getValue(),
            $attackNumber->getValue(),
            "Expected attack number {$expectedAttackNumber} on distance {$targetDistance}"
        );
    }

    /**
     * @param FightProperties $fightProperties
     * @param int $weaponBaseOfWounds
     * @param int $baseOfWoundsMalusFromSkills
     * @param int $baseOfWoundsBonusForHolding
     * @param int $baseOfWoundsModifierFromActions
     * @param int $baseOfWoundsBonusFromZoology
     */
    private function I_can_get_expected_base_of_wounds(
        FightProperties $fightProperties,
        $weaponBaseOfWounds,
        $baseOfWoundsMalusFromSkills,
        $baseOfWoundsBonusForHolding,
        $baseOfWoundsModifierFromActions,
        int $baseOfWoundsBonusFromZoology
    )
    {
        $baseOfWounds = $fightProperties->getBaseOfWounds();
        self::assertInstanceOf(WoundsBonus::class, $baseOfWounds);
        self::assertSame($baseOfWounds, $fightProperties->getBaseOfWounds(), 'Expected same instances');
        $expectedBaseOfWoundsValue = $weaponBaseOfWounds + $baseOfWoundsMalusFromSkills + $baseOfWoundsBonusForHolding
            + $baseOfWoundsModifierFromActions + $baseOfWoundsBonusFromZoology;
        self::assertSame($baseOfWounds->getValue(), $expectedBaseOfWoundsValue);
    }

    /**
     * @param FightProperties $fightProperties
     * @param int $expectedLoadingInRounds
     */
    private function I_can_get_expected_loading_in_rounds(FightProperties $fightProperties, $expectedLoadingInRounds)
    {
        $loadingInRounds = $fightProperties->getLoadingInRounds();
        self::assertInstanceOf(LoadingInRounds::class, $loadingInRounds);
        self::assertSame($expectedLoadingInRounds, $loadingInRounds->getValue());
        self::assertSame($loadingInRounds, $fightProperties->getLoadingInRounds(), 'Expected same instances');
    }

    /**
     * @param FightProperties $fightProperties
     * @param int $encounterRangeValue
     */
    private function I_can_get_expected_encounter_range(
        FightProperties $fightProperties,
        $encounterRangeValue
    )
    {
        $encounterRange = $fightProperties->getEncounterRange();
        self::assertInstanceOf(EncounterRange::class, $encounterRange);
        self::assertSame($encounterRangeValue, $encounterRange->getValue());
        self::assertSame($encounterRange, $fightProperties->getEncounterRange(), 'Expected same instances');
    }

    /**
     * @param FightProperties $fightProperties
     * @param bool $isRanged
     */
    private function I_can_get_expected_maximal_range(FightProperties $fightProperties, $isRanged)
    {
        $expectedMaximalRange = $isRanged
            ? MaximalRange::getItForRangedWeapon($fightProperties->getEncounterRange())
            : MaximalRange::getItForMeleeWeapon($fightProperties->getEncounterRange());
        $maximalRange = $fightProperties->getMaximalRange();
        self::assertInstanceOf(MaximalRange::class, $maximalRange);
        self::assertSame(
            $expectedMaximalRange->getValue(),
            $maximalRange->getValue(),
            "Expected maximal range {$expectedMaximalRange->getValue()}"
        );
        self::assertSame($maximalRange, $fightProperties->getMaximalRange(), 'Expected same instances');
    }

    /**
     * @param FightProperties $fightProperties
     * @param BodyPropertiesForFight $bodyPropertiesForFight
     * @param int $defenseNumberModifierFromCombatActions
     * @param bool $usesSimplifiedLightingRules
     * @param int $currentMalusFromLightingContrast
     * @param int $defenseNumberMalusByStrengthWithWeapon
     * @param int $coverOfWeapon
     * @param int $skillsMalusToCoverWithWeapon
     * @param int $defenseNumberMalusByStrengthWithShield
     * @param int $coverOfShield
     * @param int $skillsMalusToCoverWithShield
     * @param int $coverBonusFromZoology
     * @param int $malusFromRiding
     */
    private function I_can_get_defense_and_defense_number(
        FightProperties $fightProperties,
        BodyPropertiesForFight $bodyPropertiesForFight,
        $defenseNumberModifierFromCombatActions,
        $usesSimplifiedLightingRules,
        $currentMalusFromLightingContrast,
        $defenseNumberMalusByStrengthWithWeapon,
        $coverOfWeapon,
        $skillsMalusToCoverWithWeapon,
        $defenseNumberMalusByStrengthWithShield,
        $coverOfShield,
        $skillsMalusToCoverWithShield,
        int $coverBonusFromZoology,
        int $malusFromRiding
    )
    {
        $defense = $fightProperties->getDefense();
        self::assertInstanceOf(Defense::class, $defense);
        $expectedDefense = Defense::getIt($bodyPropertiesForFight->getAgility());
        self::assertSame($expectedDefense->getValue(), $defense->getValue());

        self::assertInstanceOf(DefenseNumber::class, $fightProperties->getDefenseNumber());
        $expectedDefenseNumber = DefenseNumber::getIt(Defense::getIt($bodyPropertiesForFight->getAgility()))
            ->add($defenseNumberModifierFromCombatActions + ($usesSimplifiedLightingRules ? 0 : $currentMalusFromLightingContrast)
                + $malusFromRiding
            );
        self::assertSame($expectedDefenseNumber->getValue(), $fightProperties->getDefenseNumber()->getValue());

        $expectedDefenseNumberWithWeapon = $expectedDefenseNumber->add(
            $defenseNumberMalusByStrengthWithWeapon
            + $coverOfWeapon
            + $skillsMalusToCoverWithWeapon
            + $coverBonusFromZoology
        );
        self::assertSame(
            $expectedDefenseNumberWithWeapon->getValue(),
            $fightProperties->getDefenseNumberWithWeaponlike()->getValue(),
            "Expected defense number with weapon to be {$expectedDefenseNumberWithWeapon->getValue()}"
        );

        $expectedDefenseNumberWithShield = $expectedDefenseNumber->add(
            $defenseNumberMalusByStrengthWithShield
            + $coverOfShield
            + $skillsMalusToCoverWithShield
            + $coverBonusFromZoology
        );
        self::assertSame(
            $expectedDefenseNumberWithShield->getValue(),
            $fightProperties->getDefenseNumberWithShield()->getValue()
        );
    }

    /**
     * @param FightProperties $fightProperties
     * @param Distance $expectedMovedDistance
     */
    private function I_can_get_moved_distance(FightProperties $fightProperties, Distance $expectedMovedDistance)
    {
        $movedDistance = $fightProperties->getMovedDistance();
        self::assertInstanceOf(Distance::class, $movedDistance);
        self::assertSame($expectedMovedDistance->getValue(), $movedDistance->getValue());
        self::assertSame($movedDistance, $fightProperties->getMovedDistance(), 'Same instances expected');
    }

    /**
     * @return \Mockery\MockInterface|Armourer
     */
    private function createArmourer()
    {
        return $this->mockery(Armourer::class);
    }

    /**
     * @param int $currentMalus
     * @return \Mockery\MockInterface|Glared
     */
    private function createGlared($currentMalus = 0)
    {
        $glared = $this->mockery(Glared::class);
        $glared->shouldReceive('getCurrentMalus')
            ->andReturn($currentMalus);

        return $glared;
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param ArmamentCode $armamentCode
     * @param Strength $expectedStrength
     * @param Size $size
     * @param bool $canUseArmament
     * @param bool $canHoldItByOneHand
     * @param bool $canHoldItByTwoHands
     * @param bool $isTwoHandedOnly
     * @return Armourer
     */
    private function addCanUseArmament(
        Armourer $armourer,
        ArmamentCode $armamentCode,
        Strength $expectedStrength,
        Size $size,
        $canUseArmament = true,
        $canHoldItByOneHand = true,
        $canHoldItByTwoHands = true,
        $isTwoHandedOnly = false
    ): Armourer
    {
        $armourer->shouldReceive('canUseArmament')
            ->zeroOrMoreTimes()
            ->with($armamentCode, \Mockery::type(Strength::class), $size)
            ->andReturnUsing(
                function (ArmamentCode $armamentCode, Strength $strength, Size $size) use ($expectedStrength, $canUseArmament) {
                    self::assertSame(
                        $expectedStrength->getValue(),
                        $strength->getValue(),
                        "Expected strength {$expectedStrength->getValue()}, got {$strength->getValue()} for {$armamentCode}"
                    );

                    return $canUseArmament;
                }
            );
        $armourer->shouldReceive('canHoldItByOneHand')
            ->zeroOrMoreTimes()
            ->with($armamentCode)
            ->andReturn($canHoldItByOneHand);
        $armourer->shouldReceive('canHoldItByTwoHands')
            ->zeroOrMoreTimes()
            ->with($armamentCode)
            ->andReturn($canHoldItByTwoHands);
        $armourer->shouldReceive('isTwoHandedOnly')
            ->zeroOrMoreTimes()
            ->with($armamentCode)
            ->andReturn($isTwoHandedOnly);

        return $armourer;
    }

    /**
     * @param Strength $strength
     * @param Size $size
     * @param Strength $strengthOfMainHand
     * @param Strength $strengthOfOffhand
     * @param Speed $speed
     * @return \Mockery\MockInterface|BodyPropertiesForFight
     */
    private function createBodyPropertiesForFight(
        Strength $strength,
        Size $size,
        Strength $strengthOfMainHand,
        Strength $strengthOfOffhand,
        Speed $speed = null
    )
    {
        $bodyPropertiesForFight = $this->mockery(BodyPropertiesForFight::class);
        $bodyPropertiesForFight->shouldReceive('getStrength')
            ->andReturn($strength);
        $bodyPropertiesForFight->shouldReceive('getSize')
            ->andReturn($size);
        $bodyPropertiesForFight->shouldReceive('getStrengthOfMainHand')
            ->andReturn($strengthOfMainHand);
        $bodyPropertiesForFight->shouldReceive('getStrengthOfOffhand')
            ->andReturn($strengthOfOffhand);
        if ($speed !== null) {
            $bodyPropertiesForFight->shouldReceive('getSpeed')
                ->andReturn($speed);
        }

        return $bodyPropertiesForFight;
    }

    /**
     * @param array $values
     * @param bool $usesSimplifiedLightingRules
     * @return \Mockery\MockInterface|CombatActions
     */
    private function createCombatActions(array $values, $usesSimplifiedLightingRules = false)
    {
        $combatActions = $this->mockery(CombatActions::class);
        $combatActions->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator($values));
        $combatActions->shouldReceive('usesSimplifiedLightingRules')
            ->andReturn($usesSimplifiedLightingRules);

        return $combatActions;
    }

    /**
     * @return \Mockery\MockInterface|Skills
     */
    private function createSkills()
    {
        return $this->mockery(Skills::class);
    }

    /**
     * @param WeaponlikeCode $weaponlikeCode
     * @param array $possibleActions
     * @param MissingWeaponSkillTable $missingWeaponSkillsTable
     * @param ShieldUsageSkillTable $missingShieldSkillsTable
     * @return \Mockery\MockInterface|Tables
     */
    private function createTables(
        WeaponlikeCode $weaponlikeCode,
        array $possibleActions,
        MissingWeaponSkillTable $missingWeaponSkillsTable = null,
        ShieldUsageSkillTable $missingShieldSkillsTable = null
    )
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getCombatActionsWithWeaponTypeCompatibilityTable')
            ->andReturn($compatibilityTable = $this->mockery(CombatActionsWithWeaponTypeCompatibilityTable::class));
        $compatibilityTable->shouldReceive('getActionsPossibleWhenFightingWith')
            ->zeroOrMoreTimes()
            ->with($weaponlikeCode)
            ->andReturn($possibleActions);
        if ($missingWeaponSkillsTable) {
            $tables->shouldReceive('getMissingWeaponSkillTable')
                ->andReturn($missingWeaponSkillsTable);
        }
        if ($missingShieldSkillsTable) {
            $tables->shouldReceive('getShieldUsageSkillTable')
                ->andReturn($missingShieldSkillsTable);
        }
        $tables->makePartial();

        return $tables;
    }

    /**
     * @param Tables|\Mockery\MockInterface $tables
     * @param WeaponlikeCode $weaponlikeCode
     * @param string $woundType
     */
    private function addWoundsTypeOf(Tables $tables, WeaponlikeCode $weaponlikeCode, string $woundType): void
    {
        $tables->shouldReceive('getWeaponlikeTableByWeaponlikeCode')
            ->atLeast()->once()
            ->with($weaponlikeCode)
            ->andReturn($weaponlikeTable = $this->mockery(WeaponlikeTable::class));
        $weaponlikeTable->shouldReceive('getWoundsTypeOf')
            ->atLeast()->once()
            ->with($weaponlikeCode)
            ->andReturn($woundType);
    }

    /**
     * @param string $name
     * @param bool $isRangedAndShooting
     * @return \Mockery\MockInterface|RangedWeaponCode|MeleeWeaponCode
     */
    private function createWeapon(string $name = 'foo', bool $isRangedAndShooting = false)
    {
        $weaponlikeCode = $this->mockery($isRangedAndShooting ? RangedWeaponCode::class : MeleeWeaponCode::class);
        $weaponlikeCode->shouldReceive('__toString')
            ->andReturn($name);
        $weaponlikeCode->shouldReceive('isShield')
            ->andReturn(false);
        $weaponlikeCode->shouldReceive('isShootingWeapon')
            ->andReturn($isRangedAndShooting);
        $weaponlikeCode->shouldReceive('isRanged')
            ->andReturn($isRangedAndShooting);
        $weaponCategories = $isRangedAndShooting
            ? WeaponCategoryCode::getRangedWeaponCategoryValues()
            : WeaponCategoryCode::getMeleeWeaponCategoryValues();
        foreach ($weaponCategories as $weaponCategory) {
            $weaponTypes = \explode('_and_', $weaponCategory);
            $weaponTypes = \array_map(function (string $weaponType) {
                return \preg_replace('~s$~', '', \str_replace('knives', 'knife', $weaponType));
            }, $weaponTypes);
            $weaponlikeCode->shouldReceive(StringTools::assembleMethodName(\implode('_or_', $weaponTypes), 'is'))
                ->andReturn(true);
        }

        return $weaponlikeCode;
    }

    /**
     * @param bool $holdsByTwoHands
     * @param bool $holdsByMainHand
     * @param bool $holdsByOffhand
     * @return MockInterface|ItemHoldingCode
     */
    private function createWeaponlikeHolding(bool $holdsByTwoHands, bool $holdsByMainHand, bool $holdsByOffhand): ItemHoldingCode
    {
        $itemHolding = $this->mockery(ItemHoldingCode::class);
        $itemHolding->shouldReceive('holdsByTwoHands')
            ->andReturn($holdsByTwoHands);
        $itemHolding->shouldReceive('holdsByOneHand')
            ->andReturn(!$holdsByTwoHands);
        $itemHolding->shouldReceive('holdsByMainHand')
            ->andReturn($holdsByMainHand);
        $itemHolding->shouldReceive('holdsByOffhand')
            ->andReturn($holdsByOffhand);
        $itemHolding->shouldReceive('getOpposite')
            ->andReturn($holdsByMainHand
                ? ItemHoldingCode::getIt(ItemHoldingCode::OFFHAND)
                : ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND)
            );

        return $itemHolding;
    }

    /**
     * @return \Mockery\MockInterface|ShieldCode
     */
    private function createShieldCode(): ShieldCode
    {
        return $this->mockery(ShieldCode::class);
    }

    private function addAgility(MockInterface $mock, Agility $agility): void
    {
        $mock->shouldReceive('getAgility')
            ->andReturn($agility);
    }

    private function addKnack(MockInterface $mock, Knack $knack): void
    {
        $mock->shouldReceive('getKnack')
            ->andReturn($knack);
    }

    private function addHeight(MockInterface $mock, Height $height): void
    {
        $mock->shouldReceive('getHeight')
            ->andReturn($height);
    }

    /**
     * @param $value
     * @return Height|\Mockery\MockInterface
     */
    private function createHeight($value)
    {
        $height = $this->mockery(Height::class);
        $height->shouldReceive('getValue')
            ->andReturn($value);
        $height->shouldReceive('__toString')
            ->andReturn((string)$value);

        return $height;
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $weaponlikeCode
     * @param Strength $expectedStrength
     * @param int $attackNumberMalus
     * @see FightProperties::getAttackNumberModifier
     */
    private function addAttackNumberMalusByStrengthWithWeaponlike(
        Armourer $armourer,
        WeaponlikeCode $weaponlikeCode,
        Strength $expectedStrength,
        $attackNumberMalus
    )
    {
        /** @noinspection PhpUnusedParameterInspection */
        $armourer->shouldReceive('getAttackNumberMalusByStrengthWithWeaponlike')
            ->atLeast()->once()
            ->with($weaponlikeCode, \Mockery::type(Strength::class))
            ->andReturnUsing(
                function (WeaponlikeCode $weaponlikeCode, Strength $strength) use ($attackNumberMalus, $expectedStrength) {
                    self::assertSame($expectedStrength->getValue(), $strength->getValue());

                    return $attackNumberMalus;
                }
            );
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param WeaponlikeCode $expectedWeaponlikeCode
     * @param MissingWeaponSkillTable $expectedMissingWeaponSkillTable
     * @param bool $fightsWithTwoWeapons
     * @param int $attackNumberMalus
     * @see FightProperties::getAttackNumberModifier
     */
    private function addMalusToAttackNumberFromSkillsWithWeaponlike(
        Skills $skills,
        WeaponlikeCode $expectedWeaponlikeCode,
        MissingWeaponSkillTable $expectedMissingWeaponSkillTable,
        $fightsWithTwoWeapons,
        $attackNumberMalus
    ): void
    {
        $skills->shouldReceive('getMalusToAttackNumberWithWeaponlike')
            ->atLeast()->once()
            ->with($expectedWeaponlikeCode, $this->type(Tables::class), $fightsWithTwoWeapons)
            ->andReturnUsing(
                function (WeaponlikeCode $weaponlikeCode, Tables $tables, $fightsWithTwoWeapons)
                use ($expectedMissingWeaponSkillTable, $attackNumberMalus) {
                    self::assertSame($expectedMissingWeaponSkillTable, $tables->getMissingWeaponSkillTable());

                    return $attackNumberMalus;
                }
            );
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $weaponlikeCode
     * @param $offensiveness
     */
    private function addOffensiveness(Armourer $armourer, WeaponlikeCode $weaponlikeCode, $offensiveness)
    {
        $armourer->shouldReceive('getOffensivenessOfWeaponlike')
            ->atLeast()->once()
            ->with($weaponlikeCode)
            ->andReturn($offensiveness);
    }

    /**
     * @param CombatActions|\Mockery\MockInterface $combatActions
     * @param $attackNumberModifier
     */
    private function addCombatActionsAttackNumber(CombatActions $combatActions, $attackNumberModifier)
    {
        $combatActions->shouldReceive('getAttackNumberModifier')
            ->andReturn($attackNumberModifier);
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param $malusFromRiding
     */
    private function addMalusToAttackNumberByRiding(Skills $skills, int $malusFromRiding)
    {
        $skills->shouldReceive('getMalusToAttackNumberWhenRiding')
            ->andReturn($malusFromRiding);
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param $malusFromRiding
     */
    private function addMalusToDefenseNumberByRiding(Skills $skills, int $malusFromRiding)
    {
        $skills->shouldReceive('getMalusToDefenseNumberWhenRiding')
            ->andReturn($malusFromRiding);
    }

    /**
     * @param CombatActions|\Mockery\MockInterface $combatActions
     * @param $attackNumberModifier
     */
    private function addCombatActionsFightNumber(CombatActions $combatActions, $attackNumberModifier)
    {
        $combatActions->shouldReceive('getFightNumberModifier')
            ->andReturn($attackNumberModifier);
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $weaponlikeCode
     * @param Strength $expectedStrength
     * @param Speed $speed
     * @param $encounterRangeValue
     */
    private function addEncounterRange(
        Armourer $armourer,
        WeaponlikeCode $weaponlikeCode,
        Strength $expectedStrength,
        Speed $speed,
        $encounterRangeValue
    ): void
    {
        /** @noinspection PhpUnusedParameterInspection */
        $armourer->shouldReceive('getEncounterRangeWithWeaponlike')
            ->atLeast()->once()
            ->with($weaponlikeCode, \Mockery::type(Strength::class), $speed)
            ->andReturnUsing(
                function (WeaponlikeCode $weaponlikeCode, Strength $strength, Speed $speed)
                use ($encounterRangeValue, $expectedStrength) {
                    self::assertSame($expectedStrength->getValue(), $strength->getValue());

                    return EncounterRange::getIt($encounterRangeValue);
                }
            );
    }

    /**
     * @param CombatActions|\Mockery\MockInterface $combatActions
     * @param bool $enemyIsFasterThanYou
     * @param int $defenseNumberModifier
     */
    private function addDefenseNumberFromActions(CombatActions $combatActions, bool $enemyIsFasterThanYou, int $defenseNumberModifier): void
    {
        $combatActions->shouldReceive($enemyIsFasterThanYou
            ? 'getDefenseNumberModifierAgainstFasterOpponent'
            : 'getDefenseNumberModifier'
        )
            ->andReturn($defenseNumberModifier);
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $weaponlikeCode
     * @param Strength $expectedStrength
     * @param int $defenseNumberMalus
     */
    private function addDefenseNumberMalusByStrength(
        Armourer $armourer,
        WeaponlikeCode $weaponlikeCode,
        Strength $expectedStrength,
        $defenseNumberMalus
    ): void
    {
        /** @noinspection PhpUnusedParameterInspection */
        $armourer->shouldReceive('getDefenseNumberMalusByStrengthWithWeaponOrShield')
            ->atLeast()->once()
            ->with($weaponlikeCode, \Mockery::type(Strength::class))
            ->andReturnUsing(
                function (WeaponlikeCode $weaponlikeCode, Strength $strength) use ($defenseNumberMalus, $expectedStrength) {
                    self::assertSame(
                        $expectedStrength->getValue(),
                        $strength->getValue(),
                        "Expected strength {$expectedStrength}, got {$strength} for {$weaponlikeCode}"
                    );

                    return $defenseNumberMalus;
                }
            );
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $weaponlikeCode
     * @param int $coverOfWeapon
     */
    private function addCoverOf(Armourer $armourer, WeaponlikeCode $weaponlikeCode, int $coverOfWeapon): void
    {
        $armourer->shouldReceive('getCoverOfWeaponOrShield')
            ->atLeast()->once()
            ->with($weaponlikeCode)
            ->andReturn($coverOfWeapon);
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $weaponlikeCode
     * @param Strength $expectedStrength
     * @param int $loadingInRounds
     */
    private function addLoadingInRoundsByStrengthWithRangedWeapon(
        Armourer $armourer,
        WeaponlikeCode $weaponlikeCode,
        Strength $expectedStrength,
        int $loadingInRounds
    ): void
    {
        $armourer->shouldReceive('getLoadingInRoundsByStrengthWithRangedWeapon')
            ->zeroOrMoreTimes()
            ->with($weaponlikeCode, \Mockery::type(Strength::class))
            ->andReturnUsing(function (RangedWeaponCode $weaponlikeCode, Strength $strength) use ($expectedStrength, $loadingInRounds) {
                self::assertSame(
                    $expectedStrength->getValue(),
                    $strength->getValue(),
                    "Expected strength {$expectedStrength} for weapon {$weaponlikeCode}"
                );

                return $loadingInRounds;
            });
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param WeaponlikeCode $weaponlikeCode
     * @param MissingWeaponSkillTable $expectedMissingWeaponSkillTable
     * @param bool $fightsWithTwoWeapons
     * @param int $skillsMalusToCoverWithWeapon
     */
    private function addSkillsMalusToCoverWithWeapon(
        Skills $skills,
        WeaponlikeCode $weaponlikeCode,
        MissingWeaponSkillTable $expectedMissingWeaponSkillTable,
        bool $fightsWithTwoWeapons,
        int $skillsMalusToCoverWithWeapon
    ): void
    {
        /** @noinspection PhpUnusedParameterInspection */
        $skills->shouldReceive('getMalusToCoverWithWeapon')
            ->atLeast()->once()
            ->with($weaponlikeCode, $this->type(Tables::class), $fightsWithTwoWeapons)
            ->andReturnUsing(
                function (WeaponlikeCode $weaponlikeCode, Tables $tables, $fightsWithTwoWeapons)
                use ($expectedMissingWeaponSkillTable, $skillsMalusToCoverWithWeapon) {
                    self::assertSame($expectedMissingWeaponSkillTable, $tables->getMissingWeaponSkillTable());

                    return $skillsMalusToCoverWithWeapon;
                }
            );
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param ShieldUsageSkillTable $expectedShieldUsageSkillTable
     * @param int $skillsMalusToCoverWithShield
     */
    private function addSkillsMalusToCoverWithShield(
        Skills $skills,
        ShieldUsageSkillTable $expectedShieldUsageSkillTable,
        int $skillsMalusToCoverWithShield
    ): void
    {
        $skills->shouldReceive('getMalusToCoverWithShield')
            ->zeroOrMoreTimes()
            ->with($this->type(Tables::class))
            ->andReturnUsing(
                function (Tables $tables)
                use ($expectedShieldUsageSkillTable, $skillsMalusToCoverWithShield) {
                    self::assertSame($expectedShieldUsageSkillTable, $tables->getShieldUsageSkillTable());

                    return $skillsMalusToCoverWithShield;
                }
            );
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $expectedWeaponlikeCode
     * @param Strength $expectedStrength
     * @param int $fightNumberMalusByStrengthWithWeapon
     * @see FightProperties::getFightNumberMalusByStrength
     */
    private function addFightNumberMalusByStrengthWithWeaponOrShield(
        Armourer $armourer,
        WeaponlikeCode $expectedWeaponlikeCode,
        Strength $expectedStrength,
        int $fightNumberMalusByStrengthWithWeapon
    ): void
    {
        /** @noinspection PhpUnusedParameterInspection */
        $armourer->shouldReceive('getFightNumberMalusByStrengthWithWeaponOrShield')
            ->atLeast()->once()
            ->with($expectedWeaponlikeCode, \Mockery::type(Strength::class))
            ->andReturnUsing(
                function (WeaponlikeCode $expectedWeaponlikeCode, Strength $strength)
                use ($fightNumberMalusByStrengthWithWeapon, $expectedStrength) {
                    self::assertSame($expectedStrength->getValue(), $strength->getValue());

                    return $fightNumberMalusByStrengthWithWeapon;
                }
            );
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param Armourer $armourer
     * @param BodyArmorCode $bodyArmorCode
     * @param int $malusToFightNumberWithBodyArmor
     * @param HelmCode $helmCode
     * @param $malusToFightNumberWithHelm
     * @param ShieldCode $shieldCode
     * @param $malusToFightNumberWithShield
     * @param ShieldCode $shieldAsWeapon = null
     * @param int $malusToFightNumberWithShieldAsWeapon = null
     * @see FightProperties::getFightNumberMalusFromProtectivesBySkills
     */
    private function addFightNumberMalusFromProtectivesBySkills(
        Skills $skills,
        Armourer $armourer,
        BodyArmorCode $bodyArmorCode,
        int $malusToFightNumberWithBodyArmor,
        HelmCode $helmCode,
        int $malusToFightNumberWithHelm,
        ShieldCode $shieldCode,
        int $malusToFightNumberWithShield,
        ShieldCode $shieldAsWeapon = null,
        int $malusToFightNumberWithShieldAsWeapon = null
    ): void
    {
        $skills->shouldReceive('getMalusToFightNumberWithProtective')
            ->atLeast()->once()
            ->with($bodyArmorCode, $armourer)
            ->andReturn($malusToFightNumberWithBodyArmor);
        $skills->shouldReceive('getMalusToFightNumberWithProtective')
            ->atLeast()->once()
            ->with($helmCode, $armourer)
            ->andReturn($malusToFightNumberWithHelm);
        $skills->shouldReceive('getMalusToFightNumberWithProtective')
            ->atLeast()->once()
            ->with($shieldCode, $armourer)
            ->andReturn($malusToFightNumberWithShield);
        if ($shieldAsWeapon) {
            $skills->shouldReceive('getMalusToFightNumberWithProtective')
                ->atLeast()->once()
                ->with($shieldAsWeapon, $armourer)
                ->andReturn($malusToFightNumberWithShieldAsWeapon);
        }
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param int $fightNumberBonus
     */
    private function addAttackNumberBonusByZoologySkill(Skills $skills, int $fightNumberBonus): void
    {
        $skills->shouldReceive('getBonusToAttackNumberAgainstFreeWillAnimal')
            ->andReturn($fightNumberBonus);
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param int $fightNumberBonus
     */
    private function addCoverBonusByZoologySkill(Skills $skills, int $fightNumberBonus): void
    {
        $skills->shouldReceive('getBonusToCoverAgainstFreeWillAnimal')
            ->andReturn($fightNumberBonus);
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param int $fightNumberBonus
     */
    private function addBaseOfWoundsBonusByZoologySkill(Skills $skills, int $fightNumberBonus): void
    {
        $skills->shouldReceive('getBonusToBaseOfWoundsAgainstFreeWillAnimal')
            ->andReturn($fightNumberBonus);
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param WeaponlikeCode $weaponlikeCode
     * @param MissingWeaponSkillTable $expectedMissingWeaponSkillTable
     * @param bool $fightsWithTwoWeapons
     * @param int $malusFromWeaponlike
     * @see FightProperties::getFightNumberMalusFromWeaponlikesBySkills
     */
    private function addMalusToFightNumberWithWeaponlike(
        Skills $skills,
        WeaponlikeCode $weaponlikeCode,
        MissingWeaponSkillTable $expectedMissingWeaponSkillTable,
        bool $fightsWithTwoWeapons,
        int $malusFromWeaponlike
    ): void
    {
        /** @noinspection PhpUnusedParameterInspection */
        $skills->shouldReceive('getMalusToFightNumberWithWeaponlike')
            ->atLeast()->once()
            ->with($weaponlikeCode, $this->type(Tables::class), $fightsWithTwoWeapons)
            ->andReturnUsing(
                function (WeaponlikeCode $weaponlikeCode, Tables $tables, $fightsWithTwoWeapons)
                use ($expectedMissingWeaponSkillTable, $malusFromWeaponlike) {
                    self::assertSame($expectedMissingWeaponSkillTable, $tables->getMissingWeaponSkillTable());

                    return $malusFromWeaponlike;
                }
            );
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param int $malusFromRiding
     * @see FightProperties::getFightNumberMalusFromWeaponlikesBySkills
     */
    private function addMalusToFightNumberByRiding(Skills $skills, int $malusFromRiding): void
    {
        $skills->shouldReceive('getMalusToFightNumberWhenRiding')
            ->andReturn($malusFromRiding);
    }

    /**
     * @param Tables|\Mockery\MockInterface $tables
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $weaponlikeCode
     * @param int $lengthOfWeaponlike
     * @param ShieldCode $shieldCode
     * @param int $lengthOfShield
     * @see FightProperties::getLongerWeaponlike
     */
    private function addFightNumberBonusByWeaponlikeLength(
        Tables $tables,
        Armourer $armourer,
        WeaponlikeCode $weaponlikeCode,
        int $lengthOfWeaponlike,
        ShieldCode $shieldCode,
        int $lengthOfShield
    ): void
    {
        $tables->shouldReceive('getMeleeWeaponlikeTableByMeleeWeaponlikeCode')
            ->with($weaponlikeCode)
            ->andReturn($meleeWeaponlikesTable = $this->mockery(MeleeWeaponlikesTable::class));
        $meleeWeaponlikesTable->shouldReceive('getLengthOf')
            ->with($shieldCode)
            ->andReturn($lengthOfShield);
        $meleeWeaponlikesTable->shouldReceive('getLengthOf')
            ->with($weaponlikeCode)
            ->andReturn($lengthOfWeaponlike);
        $armourer->shouldReceive('getLengthOfWeaponOrShield')
            ->atLeast()->once()
            ->with($weaponlikeCode)
            ->andReturn($lengthOfWeaponlike);
        $armourer->shouldReceive('getLengthOfWeaponOrShield')
            ->atLeast()->once()
            ->with($shieldCode)
            ->andReturn($lengthOfShield);
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $weaponlikeCode
     * @param Strength $expectedStrength
     * @param int $baseOfWounds
     */
    private function addWeaponBaseOfWounds(Armourer $armourer, WeaponlikeCode $weaponlikeCode, Strength $expectedStrength, int $baseOfWounds): void
    {
        $armourer->shouldReceive('getBaseOfWoundsUsingWeaponlike')
            ->atLeast()->once()
            ->with($weaponlikeCode, \Mockery::type(Strength::class))
            ->andReturnUsing(
                function (WeaponlikeCode $weaponlikeCode, Strength $strength) use ($baseOfWounds, $expectedStrength) {
                    self::assertSame($expectedStrength->getValue(), $strength->getValue());

                    return $baseOfWounds;
                }
            );
    }

    /**
     * @param Skills|\Mockery\MockInterface $skills
     * @param WeaponlikeCode $weaponlikeCode
     * @param MissingWeaponSkillTable $expectedMissingWeaponSkillTable
     * @param bool $fightsWithTwoWeapons
     * @param int $baseOfWoundsMalusFromSkills
     */
    private function addBaseOfWoundsMalusFromSkills(
        Skills $skills,
        WeaponlikeCode $weaponlikeCode,
        MissingWeaponSkillTable $expectedMissingWeaponSkillTable,
        bool $fightsWithTwoWeapons,
        int $baseOfWoundsMalusFromSkills
    ): void
    {
        $skills->shouldReceive('getMalusToBaseOfWoundsWithWeaponlike')
            ->atLeast()->once()
            ->with($weaponlikeCode, $this->type(Tables::class), $fightsWithTwoWeapons)
            ->andReturnUsing(
                function (WeaponlikeCode $weaponlikeCode, Tables $tables, $fightsWithTwoWeapons)
                use ($expectedMissingWeaponSkillTable, $baseOfWoundsMalusFromSkills) {
                    self::assertSame($expectedMissingWeaponSkillTable, $tables->getMissingWeaponSkillTable());

                    return $baseOfWoundsMalusFromSkills;
                }
            );
    }

    /**
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param WeaponlikeCode $weaponlikeCode
     * @param $weaponHolding
     * @param int $bonusFromHolding
     */
    private function addBaseOfWoundsBonusByHolding(
        Armourer $armourer,
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponHolding,
        int $bonusFromHolding
    ): void
    {
        $armourer->shouldReceive('getBaseOfWoundsBonusForHolding')
            ->atLeast()->once()
            ->with($weaponlikeCode, $weaponHolding)
            ->andReturn($bonusFromHolding);
    }

    /**
     * @param CombatActions|\Mockery\MockInterface $combatActions
     * @param bool $weaponIsCrushing
     * @param int $baseOfWoundsModifierFromActions
     */
    private function addBaseOfWoundsModifierFromActions(CombatActions $combatActions, bool $weaponIsCrushing, int $baseOfWoundsModifierFromActions): void
    {
        $combatActions->shouldReceive('getBaseOfWoundsModifier')
            ->atLeast()->once()
            ->with($weaponIsCrushing)
            ->andReturn($baseOfWoundsModifierFromActions);
    }

    /**
     * @param CombatActions|\Mockery\MockInterface $combatActions
     * @param int $speedModifier
     */
    private function addActionsSpeedModifier(CombatActions $combatActions, int $speedModifier): void
    {
        $combatActions->shouldReceive('getSpeedModifier')
            ->andReturn($speedModifier);
    }

    /**
     * @param Tables|\Mockery\MockInterface $tables
     * @param Speed $speed
     * @param int $speedModifier
     * @param Distance $movedDistance
     */
    private function addDistanceTable(Tables $tables, Speed $speed, $speedModifier, Distance $movedDistance): void
    {
        $tables->shouldReceive('getDistanceTable')
            ->andReturn($distanceTable = $this->mockery(DistanceTable::class));
        $distanceTable->shouldReceive('toDistance')
            ->atLeast()->once()
            ->with(\Mockery::type(DistanceBonus::class))
            ->andReturnUsing(function (DistanceBonus $distanceBonus) use ($speed, $speedModifier, $movedDistance) {
                self::assertSame($distanceBonus->getValue(), $speed->getValue() + $speedModifier);

                return $movedDistance;
            });
    }

    /**
     * @param float $value
     * @return \Mockery\MockInterface|Distance
     */
    private function createDistance(float $value = null): Distance
    {
        $distance = $this->mockery(Distance::class);
        if ($value !== null) {
            $distance->shouldReceive('getValue')
                ->andReturn($value);
        }

        return $distance;
    }

    /**
     * @param Distance $distance
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param EncounterRange $encounterRange
     * @param MaximalRange $maximalRange
     * @param int $modifierByDistance
     */
    private function addAttackNumberModifierByDistance(
        Distance $distance,
        Armourer $armourer,
        EncounterRange $encounterRange,
        MaximalRange $maximalRange,
        int $modifierByDistance
    ): void
    {
        $armourer->shouldReceive('getAttackNumberModifierByDistance')
            ->atLeast()->once()
            ->with($distance, $encounterRange, $maximalRange)
            ->andReturn($modifierByDistance);
    }

    /**
     * @param Size $targetSize
     * @param Armourer|\Mockery\MockInterface $armourer
     * @param int $modifierBySize
     */
    private function addAttackNumberModifierBySize(Size $targetSize, Armourer $armourer, int $modifierBySize): void
    {
        $armourer->shouldReceive('getAttackNumberModifierBySize')
            ->atLeast()->once()
            ->with($targetSize)
            ->andReturn($modifierBySize);
    }

    /**
     * @param int $value
     * @param bool $canBeIncreased
     * @return \Mockery\MockInterface|Speed
     */
    private function createSpeed(int $value = null, bool $canBeIncreased = true): Speed
    {
        $speed = $this->mockery(Speed::class);
        if ($value !== null) {
            $speed->shouldReceive('getValue')
                ->andReturn($value);
        }
        if ($canBeIncreased) {
            $speed->shouldReceive('add')
                ->zeroOrMoreTimes()
                ->andReturnUsing(function ($valueToAdd) use ($value) {
                    return $this->createSpeed($value + $valueToAdd, false /* to avoid infinite loop */);
                });
        }

        return $speed;
    }

    /**
     * @param Armourer|MockInterface $armourer
     * @param WeaponlikeCode $weaponOrShield
     * @param Strength $strengthForMainHand
     * @param ItemHoldingCode $itemHoldingCode
     * @param Strength $usedStrength
     */
    private function addStrengthForWeaponOrShield(
        Armourer $armourer,
        WeaponlikeCode $weaponOrShield,
        Strength $strengthForMainHand,
        ItemHoldingCode $itemHoldingCode,
        Strength $usedStrength
    ): void
    {
        $armourer->shouldReceive('getStrengthForWeaponOrShield')
            ->zeroOrMoreTimes()
            ->with($weaponOrShield, $itemHoldingCode, $strengthForMainHand)
            ->andReturn($usedStrength);
    }

    // NEGATIVE TESTS

    /**
     * @test
     * @dataProvider provideArmamentsBearing
     * @param bool $weaponIsBearable
     * @param bool $shieldIsBearable
     * @param bool $armorIsBearable
     * @param bool $helmIsBearable
     */
    public function I_can_not_create_it_with_unbearable_weapon_and_shield(
        bool $weaponIsBearable,
        bool $shieldIsBearable,
        bool $armorIsBearable,
        bool $helmIsBearable
    ): void
    {
        $this->expectException(\DrdPlus\FightProperties\Exceptions\CanNotUseArmamentBecauseOfMissingStrength::class);
        $armourer = $this->createArmourer();

        $weaponlikeCode = $this->createWeapon();
        $strengthOfMainHand = Strength::getIt(123);
        $size = Size::getIt(456);
        $this->addCanUseArmament($armourer, $weaponlikeCode, $strengthOfMainHand, $size, $weaponIsBearable);

        $shieldCode = $this->createShieldCode();
        $strengthOfOffhand = Strength::getIt(234);
        $this->addCanUseArmament($armourer, $shieldCode, $strengthOfOffhand, $size, $shieldIsBearable);

        $strength = Strength::getIt(698);
        $bodyArmorCode = BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        $this->addCanUseArmament($armourer, $bodyArmorCode, $strength, $size, $armorIsBearable);
        $helmCode = HelmCode::getIt(HelmCode::WITHOUT_HELM);
        $this->addCanUseArmament($armourer, $helmCode, $strength, $size, $helmIsBearable);
        $this->addStrengthForWeaponOrShield(
            $armourer,
            $weaponlikeCode,
            $strengthOfMainHand,
            $weaponlikeHolding = $this->createWeaponlikeHolding(
                false, /* does not keep weapon by both hands now */
                true, /* holds weapon by main hand */
                false /* does not hold weapon by offhand */
            ),
            $strengthOfMainHand
        );
        $this->addStrengthForWeaponOrShield(
            $armourer,
            $shieldCode,
            $strengthOfMainHand,
            $this->getShieldHolding($weaponlikeHolding),
            $strengthOfOffhand
        );

        new FightProperties(
            $this->createBodyPropertiesForFight($strength, $size, $strengthOfMainHand, $strengthOfOffhand),
            $this->createCombatActions($combatActionValues = ['foo']),
            $this->createSkills(),
            $bodyArmorCode,
            $helmCode,
            ProfessionCode::getIt(ProfessionCode::RANGER),
            $this->createTables($weaponlikeCode, $combatActionValues),
            $armourer,
            $weaponlikeCode,
            $weaponlikeHolding,
            false, // does not fight with two weapons now
            $shieldCode,
            false, // enemy is not faster now
            $this->createGlared(),
            false, // not riding (whatever here)
            false // not fighting animal (whatever here)
        );
    }

    public function provideArmamentsBearing(): array
    {
        return [
            [false, true, true, true],
            [true, false, true, true],
            [true, true, false, true],
            [true, true, true, false],
        ];
    }

    /**
     * @test
     * @dataProvider provideWeaponOrShieldInvalidTwoHandsHolding
     * @param bool $fightsWithTwoWeapons
     * @param bool $holdsByTwoHands
     * @param bool $canHoldByOneHand
     */
    public function I_can_not_create_it_with_two_hands_holding_if_not_possible(
        bool $fightsWithTwoWeapons,
        bool $holdsByTwoHands,
        bool $canHoldByOneHand
    ): void
    {
        $this->expectException(\DrdPlus\FightProperties\Exceptions\CanNotHoldItByTwoHands::class);
        $armourer = $this->createArmourer();

        $weaponlikeCode = $this->createWeapon();
        $strengthOfMainHand = Strength::getIt(123);
        $strengthForWeapon = $holdsByTwoHands && $canHoldByOneHand
            ? $strengthOfMainHand->add(2)
            : $strengthOfMainHand;
        $size = Size::getIt(456);
        $this->addCanUseArmament($armourer, $weaponlikeCode, $strengthForWeapon, $size, true, $canHoldByOneHand, !$canHoldByOneHand);

        $shieldCode = ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        $strengthOfOffhand = Strength::getIt(234);
        $this->addCanUseArmament($armourer, $shieldCode, $strengthOfOffhand, $size);

        $strength = Strength::getIt(698);
        $bodyArmorCode = BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        $this->addCanUseArmament($armourer, $bodyArmorCode, $strength, $size);
        $helmCode = HelmCode::getIt(HelmCode::WITHOUT_HELM);
        $this->addCanUseArmament($armourer, $helmCode, $strength, $size);

        new FightProperties(
            $this->createBodyPropertiesForFight($strength, $size, $strengthOfMainHand, $strengthOfOffhand),
            $this->createCombatActions($combatActionValues = ['foo']),
            $this->createSkills(),
            $bodyArmorCode,
            $helmCode,
            ProfessionCode::getIt(ProfessionCode::RANGER),
            $this->createTables($weaponlikeCode, $combatActionValues),
            $armourer,
            $weaponlikeCode,
            $this->createWeaponlikeHolding(
                $holdsByTwoHands,
                true, /* holds weapon by main hand */
                false /* does not hold weapon by offhand */
            ),
            $fightsWithTwoWeapons,
            $shieldCode,
            false, // enemy is not faster now
            $this->createGlared(),
            false, // not riding (whatever here)
            false // not fighting animal (whatever here)
        );
    }

    public function provideWeaponOrShieldInvalidTwoHandsHolding(): array
    {
        return [
            [true, true, false],
            [false, true, true],
        ];
    }

    /**
     * @test
     */
    public function I_can_not_create_it_with_one_hand_holding_if_not_possible(): void
    {
        $this->expectException(\DrdPlus\FightProperties\Exceptions\CanNotHoldItByOneHand::class);
        $armourer = $this->createArmourer();

        $weaponlikeCode = $this->createWeapon();
        $strengthOfMainHand = Strength::getIt(123);
        $size = Size::getIt(456);
        $this->addCanUseArmament($armourer, $weaponlikeCode, $strengthOfMainHand, $size, true, false /* can not hold by one hand */);

        $shieldCode = ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        $strengthOfOffhand = Strength::getIt(234);
        $this->addCanUseArmament($armourer, $shieldCode, $strengthOfOffhand, $size);

        $strength = Strength::getIt(698);
        $bodyArmorCode = BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        $this->addCanUseArmament($armourer, $bodyArmorCode, $strength, $size);
        $helmCode = HelmCode::getIt(HelmCode::WITHOUT_HELM);
        $this->addCanUseArmament($armourer, $helmCode, $strength, $size);

        new FightProperties(
            $this->createBodyPropertiesForFight($strength, $size, $strengthOfMainHand, $strengthOfOffhand),
            $this->createCombatActions($combatActionValues = ['foo']),
            $this->createSkills(),
            $bodyArmorCode,
            $helmCode,
            ProfessionCode::getIt(ProfessionCode::RANGER),
            $this->createTables($weaponlikeCode, $combatActionValues),
            $armourer,
            $weaponlikeCode,
            $this->createWeaponlikeHolding(
                false, // does not hold it by two hands
                true, /* holds weapon by main hand */
                false /* does not hold weapon by offhand */
            ),
            true, // fights with two weapons (does not affect this test)
            $shieldCode,
            false, // enemy is not faster now
            $this->createGlared(),
            false, // not riding (whatever here)
            false // not fighting animal (whatever here)
        );
    }

    /**
     * @test
     */
    public function I_can_not_create_it_with_weapon_incompatible_actions(): void
    {
        $this->expectException(\DrdPlus\FightProperties\Exceptions\ImpossibleActionsWithCurrentWeaponlike::class);
        $this->expectExceptionMessageRegExp('~foo~');
        $armourer = $this->createArmourer();

        $weaponlikeCode = $this->createWeapon();
        $strengthOfMainHand = Strength::getIt(123);
        $size = Size::getIt(456);
        $this->addCanUseArmament($armourer, $weaponlikeCode, $strengthOfMainHand, $size);

        $shieldCode = ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        $strengthOfOffhand = Strength::getIt(234);
        $this->addCanUseArmament($armourer, $shieldCode, $strengthOfOffhand, $size);

        $strength = Strength::getIt(698);
        $bodyArmorCode = BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        $this->addCanUseArmament($armourer, $bodyArmorCode, $strength, $size);
        $helmCode = HelmCode::getIt(HelmCode::WITHOUT_HELM);
        $this->addCanUseArmament($armourer, $helmCode, $strength, $size);

        new FightProperties(
            $this->createBodyPropertiesForFight($strength, $size, $strengthOfMainHand, $strengthOfOffhand),
            $this->createCombatActions($combatActionValues = ['foo']),
            $this->createSkills(),
            $bodyArmorCode,
            $helmCode,
            ProfessionCode::getIt(ProfessionCode::RANGER),
            $this->createTables($weaponlikeCode, ['bar'] /* different combat actions possible */),
            $armourer,
            $weaponlikeCode,
            $this->createWeaponlikeHolding(
                false, // does not hold it by two hands
                true, // holds weapon by main hand
                false /* does not hold weapon by offhand */
            ),
            true, // fights with two weapons (does not affect this test)
            $shieldCode,
            false, // enemy is not faster now (does not affect this test)
            $this->createGlared(),
            false, // not riding (whatever here)
            false // not fighting animal (whatever here)
        );
    }

    /**
     * @test
     */
    public function I_can_not_create_it_with_unknown_holding(): void
    {
        $this->expectException(\DrdPlus\FightProperties\Exceptions\UnknownWeaponHolding::class);
        $armourer = $this->createArmourer();

        $weaponlikeCode = $this->createWeapon();
        $strengthOfMainHand = Strength::getIt(123);
        $size = Size::getIt(456);
        $this->addCanUseArmament($armourer, $weaponlikeCode, $strengthOfMainHand, $size);

        $shieldCode = ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        $strengthOfOffhand = Strength::getIt(234);
        $this->addCanUseArmament($armourer, $shieldCode, $strengthOfOffhand, $size);

        $strength = Strength::getIt(698);
        $bodyArmorCode = BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        $this->addCanUseArmament($armourer, $bodyArmorCode, $strength, $size);
        $helmCode = HelmCode::getIt(HelmCode::WITHOUT_HELM);
        $this->addCanUseArmament($armourer, $helmCode, $strength, $size);

        new FightProperties(
            $this->createBodyPropertiesForFight($strength, $size, $strengthOfMainHand, $strengthOfOffhand),
            $this->createCombatActions($combatActionValues = ['foo']),
            $this->createSkills(),
            $bodyArmorCode,
            $helmCode,
            ProfessionCode::getIt(ProfessionCode::RANGER),
            $this->createTables($weaponlikeCode, $combatActionValues),
            $armourer,
            $weaponlikeCode,
            $this->createWeaponlikeHolding(
                false, // does not hold weapon by two hands
                false, // does not hold weapon by main hand
                false // does not hold weapon by offhand
            ),
            true, // fights with two weapons (does not affect this test)
            $shieldCode,
            false, // enemy is not faster now (does not affect this test)
            $this->createGlared(),
            false, // not riding (whatever here)
            false // not fighting animal (whatever here)
        );
    }

    /**
     * @test
     */
    public function I_can_not_use_shield_when_holding_weapon_by_two_hands(): void
    {
        $this->expectException(\DrdPlus\FightProperties\Exceptions\NoHandLeftForShield::class);
        $this->expectExceptionMessageRegExp('~buckler when holding foo with~');
        $armourer = $this->createArmourer();

        $weaponlikeCode = $this->createWeapon();
        $strengthOfMainHand = Strength::getIt(123);
        $size = Size::getIt(456);
        $this->addCanUseArmament($armourer, $weaponlikeCode, $strengthOfMainHand, $size, true, true, true, true /* two handed only */);

        $shieldCode = ShieldCode::getIt(ShieldCode::BUCKLER);
        $strengthOfOffhand = Strength::getIt(234);
        $this->addCanUseArmament($armourer, $shieldCode, $strengthOfOffhand, $size);

        $strength = Strength::getIt(698);
        $bodyArmorCode = BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        $this->addCanUseArmament($armourer, $bodyArmorCode, $strength, $size);
        $helmCode = HelmCode::getIt(HelmCode::WITHOUT_HELM);
        $this->addCanUseArmament($armourer, $helmCode, $strength, $size);

        new FightProperties(
            $this->createBodyPropertiesForFight($strength, $size, $strengthOfMainHand, $strengthOfOffhand),
            $this->createCombatActions($combatActionValues = ['bar']),
            $this->createSkills(),
            $bodyArmorCode,
            $helmCode,
            ProfessionCode::getIt(ProfessionCode::RANGER),
            $this->createTables($weaponlikeCode, $combatActionValues),
            $armourer,
            $weaponlikeCode,
            ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS),
            false,
            $shieldCode,
            false, // enemy is not faster now (does not affect this test)
            $this->createGlared(),
            false, // not riding (whatever here)
            false // not fighting animal (whatever here)
        );
    }

}