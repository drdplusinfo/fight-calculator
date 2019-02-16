<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\Physical\PhysicalSkillRank;
use DrdPlus\Skills\Physical\ShieldUsage;
use DrdPlus\Tables\Armaments\Shields\ShieldUsageSkillTable;
use DrdPlus\Tables\Armaments\Weapons\MissingWeaponSkillTable;
use DrdPlus\Tables\Tables;

class ShieldUsageTest extends PhysicalSkillTest
{
    /**
     * @test
     */
    public function I_can_get_bonus_to_restriction()
    {
        $swimming = new ShieldUsage($this->createProfessionLevel());

        self::assertSame(0, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(0, $swimming->getBonusToRestriction());

        $swimming->increaseSkillRank($this->createPhysicalSkillPoint());
        self::assertSame(1, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(1, $swimming->getBonusToRestriction());

        $swimming->increaseSkillRank($this->createPhysicalSkillPoint());
        self::assertSame(2, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(2, $swimming->getBonusToRestriction());

        $swimming->increaseSkillRank($this->createPhysicalSkillPoint());
        self::assertSame(3, $swimming->getCurrentSkillRank()->getValue());
        self::assertSame(3, $swimming->getBonusToRestriction());
    }

    /**
     * @return \Mockery\MockInterface|ProfessionFirstLevel
     */
    private function createProfessionLevel()
    {
        return $this->mockery(ProfessionFirstLevel::class);
    }

    /**
     * @return \Mockery\MockInterface|PhysicalSkillPoint
     */
    private function createPhysicalSkillPoint(): PhysicalSkillPoint
    {
        $physicalSkillPoint = $this->mockery(PhysicalSkillPoint::class);
        $physicalSkillPoint->shouldReceive('getValue')
            ->andReturn(1);

        return $physicalSkillPoint;
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_fight_number()
    {
        $shieldUsage = new ShieldUsage($this->createProfessionFirstLevel());

        $tables = $this->createTables(0, 456, 'getRestrictionBonusForSkillRank', 123, 'getFightNumberMalusForSkillRank');
        self::assertSame(123, $shieldUsage->getMalusToFightNumber($tables, -5));

        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $tables = $this->createTables(3, 3, 'getRestrictionBonusForSkillRank', 123, 'getFightNumberMalusForSkillRank');
        self::assertSame(121, $shieldUsage->getMalusToFightNumber($tables, -5));
    }

    /**
     * @param int $expectedSkillRank
     * @param int $shieldBonus
     * @param string $shieldBonusMethodName
     * @param int $weaponBonus
     * @param string $weaponBonusMethodName
     * @return \Mockery\MockInterface|Tables
     */
    private function createTables($expectedSkillRank, $shieldBonus, $shieldBonusMethodName, $weaponBonus = null, $weaponBonusMethodName = null)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getShieldUsageSkillTable')
            ->andReturn($shieldUsageSkillTable = $this->mockery(ShieldUsageSkillTable::class));
        if ($shieldBonusMethodName !== null) {
            $shieldUsageSkillTable->shouldReceive($shieldBonusMethodName)
                ->with($this->type(PhysicalSkillRank::class))
                ->atLeast()->once()
                ->andReturnUsing(function (PhysicalSkillRank $physicalSkillRank) use ($expectedSkillRank, $shieldBonus) {
                    self::assertSame($expectedSkillRank, $physicalSkillRank->getValue());

                    return $shieldBonus;
                });
        }
        $tables->shouldReceive('getMissingWeaponSkillTable')
            ->andReturn($missingWeaponsSkillTable = $this->mockery(MissingWeaponSkillTable::class));
        if ($weaponBonusMethodName !== null) {
            $missingWeaponsSkillTable->shouldReceive($weaponBonusMethodName)
                ->with(0)// it should be always called with zero (because there is nothing like 'Fight with shield' skill)
                ->atLeast()->once()
                ->andReturn($weaponBonus);
        }

        return $tables;
    }

    /**
     * @test
     */
    public function I_can_get_restriction_with_shield()
    {
        $shieldUsage = new ShieldUsage($this->createProfessionFirstLevel());
        $tables = $this->createTables(0, 3, 'getRestrictionBonusForSkillRank');
        self::assertSame(-2, $shieldUsage->getRestrictionWithShield($tables, -5));

        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $tables = $this->createTables(1, 5, 'getRestrictionBonusForSkillRank');
        self::assertSame(-7, $shieldUsage->getRestrictionWithShield($tables, -12));
    }

    /**
     * @test
     */
    public function I_get_zero_as_restriction_with_shield_even_if_bonus_is_higher_than_malus()
    {
        $shieldUsage = new ShieldUsage($this->createProfessionFirstLevel());
        $tables = $this->createTables(0, 456, 'getRestrictionBonusForSkillRank');
        self::assertSame(0, $shieldUsage->getRestrictionWithShield($tables, -5));

        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $tables = $this->createTables(2, 10, 'getRestrictionBonusForSkillRank');
        self::assertSame(0, $shieldUsage->getRestrictionWithShield($tables, -10));
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_attack_number_always_as_with_zero_skill()
    {
        $shieldUsage = new ShieldUsage($this->createProfessionFirstLevel());
        $tables = $this->createTables(null, null, null, -456, 'getAttackNumberMalusForSkillRank');
        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        self::assertSame(
            -456,
            $shieldUsage->getMalusToAttackNumber($tables),
            'I should get same malus to attack number regardless to shield usage skill'
        );
    }

    /**
     * @test
     */
    public function I_can_cover_with_shield()
    {
        $shieldUsage = new ShieldUsage($this->createProfessionFirstLevel());
        $tables = $this->createTables(0, 5, 'getCoverMalusForSkillRank');
        self::assertSame(5, $shieldUsage->getMalusToCover($tables));

        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $tables = $this->createTables(2, 11, 'getCoverMalusForSkillRank');
        self::assertSame(11, $shieldUsage->getMalusToCover($tables));
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_base_of_wounds_always_as_with_zero_skill()
    {
        $shieldUsage = new ShieldUsage($this->createProfessionFirstLevel());
        $tables = $this->createTables(null, null, null, -123, 'getBaseOfWoundsMalusForSkillRank');

        self::assertSame(-123, $shieldUsage->getMalusToBaseOfWounds($tables));

        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        $shieldUsage->increaseSkillRank($this->createSkillPoint());
        self::assertSame(
            -123,
            $shieldUsage->getMalusToBaseOfWounds($tables),
            'I should get same malus to base of wounds regardless to shield usage skill'
        );
    }
}