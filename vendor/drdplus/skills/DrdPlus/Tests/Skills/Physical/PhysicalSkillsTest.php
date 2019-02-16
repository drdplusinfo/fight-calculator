<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\ProtectiveArmamentCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Armaments\WeaponCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Professions\Commoner;
use DrdPlus\Skills\Physical\FightWithShields;
use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\Physical\PhysicalSkillRank;
use DrdPlus\Skills\Physical\FightUnarmed;
use DrdPlus\Skills\Physical\FightWithAxes;
use DrdPlus\Skills\Physical\FightWithKnivesAndDaggers;
use DrdPlus\Skills\Physical\FightWithMacesAndClubs;
use DrdPlus\Skills\Physical\FightWithMorningstarsAndMorgensterns;
use DrdPlus\Skills\Physical\FightWithSabersAndBowieKnives;
use DrdPlus\Skills\Physical\FightWithStaffsAndSpears;
use DrdPlus\Skills\Physical\FightWithSwords;
use DrdPlus\Skills\Physical\FightWithThrowingWeapons;
use DrdPlus\Skills\Physical\FightWithTwoWeapons;
use DrdPlus\Skills\Physical\FightWithVoulgesAndTridents;
use DrdPlus\Skills\Physical\PhysicalSkills;
use DrdPlus\Armourer\Armourer;
use DrdPlus\Tables\Armaments\Shields\ShieldUsageSkillTable;
use DrdPlus\Tables\Armaments\Weapons\MissingWeaponSkillTable;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Skills\SameTypeSkillsTest;
use Granam\Integer\PositiveInteger;

class PhysicalSkillsTest extends SameTypeSkillsTest
{

    /**
     * @test
     */
    public function I_can_get_unused_skill_points_from_first_level(): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $professionLevels = $this->createProfessionLevels(
            $firstLevelStrength = 123, $firstLevelAgility = 456, $nextLevelStrength = 321, $nextLevelAgility = 654
        );

        self::assertSame(
            $firstLevelStrength + $firstLevelAgility,
            $skills->getUnusedFirstLevelPhysicalSkillPointsValue($professionLevels)
        );

        $professionFirstLevel = $this->createProfessionFirstLevel();
        $skills->getArmorWearing()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // 1
        $skills->getArmorWearing()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // 2
        $skills->getArmorWearing()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // 3
        $skills->getAthletics()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // 1
        $skills->getAthletics()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel())); // 2 - from next level
        self::assertSame(
            ($firstLevelStrength + $firstLevelAgility) - (1 + 2 + 3 + 1),
            $skills->getUnusedFirstLevelPhysicalSkillPointsValue($professionLevels),
            'Expected ' . (($firstLevelStrength + $firstLevelAgility) - (1 + 2 + 3 + 1))
        );
        self::assertSame(
            ($nextLevelStrength + $nextLevelAgility) - 2,
            $skills->getUnusedNextLevelsPhysicalSkillPointsValue($professionLevels),
            'Expected ' . (($nextLevelStrength + $nextLevelAgility) - 2)
        );
    }

    /**
     * @param int $firstLevelStrengthModifier
     * @param int $firstLevelAgilityModifier
     * @param int $nextLevelsStrengthModifier
     * @param int $nextLevelsAgilityModifier
     * @return \Mockery\MockInterface|ProfessionLevels
     */
    private function createProfessionLevels(
        $firstLevelStrengthModifier,
        $firstLevelAgilityModifier,
        $nextLevelsStrengthModifier,
        $nextLevelsAgilityModifier
    ): ProfessionLevels
    {
        $professionLevels = $this->mockery(ProfessionLevels::class);
        $professionLevels->shouldReceive('getFirstLevelStrengthModifier')
            ->andReturn($firstLevelStrengthModifier);
        $professionLevels->shouldReceive('getFirstLevelAgilityModifier')
            ->andReturn($firstLevelAgilityModifier);
        $professionLevels->shouldReceive('getNextLevelsStrengthModifier')
            ->andReturn($nextLevelsStrengthModifier);
        $professionLevels->shouldReceive('getNextLevelsAgilityModifier')
            ->andReturn($nextLevelsAgilityModifier);

        return $professionLevels;
    }

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Exceptions\CanNotUseZeroSkillPointForNonZeroSkillRank
     * @expectedExceptionMessageRegExp ~0~
     */
    public function I_can_not_increase_rank_by_zero_skill_point(): void
    {
        $skills = new PhysicalSkills($professionZeroLevel = ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $skills->getAthletics()->increaseSkillRank(PhysicalSkillPoint::createZeroSkillPoint($professionZeroLevel));
    }

    /**
     * @test
     */
    public function I_can_get_unused_skill_points_from_next_levels(): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $professionLevels = $this->createProfessionLevels(
            $firstLevelStrength = 123, $firstLevelAgility = 456, $nextLevelsStrength = 321, $nextLevelsAgility = 654
        );

        self::assertSame($nextLevelsStrength + $nextLevelsAgility, $skills->getUnusedNextLevelsPhysicalSkillPointsValue($professionLevels));
        $professionFirstLevel = $this->createProfessionFirstLevel();
        $skills->getBlacksmithing()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // 1 - first level
        $professionNextLevel = $this->createProfessionNextLevel();
        $skills->getBlacksmithing()->increaseSkillRank($this->createSkillPoint($professionNextLevel)); // 2 - next level
        self::assertSame($firstLevelStrength + $firstLevelAgility - 1, $skills->getUnusedFirstLevelPhysicalSkillPointsValue($professionLevels));
        self::assertSame($nextLevelsStrength + $nextLevelsAgility - 2, $skills->getUnusedNextLevelsPhysicalSkillPointsValue($professionLevels));

        $skills->getBoatDriving()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // 1 - first level
        $skills->getBoatDriving()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // 2 - first level
        $skills->getBoatDriving()->increaseSkillRank($this->createSkillPoint($professionNextLevel)); // 3 - next level
        $skills->getFlying()->increaseSkillRank($this->createSkillPoint($professionNextLevel)); // 1 - next level
        self::assertSame(
            ($firstLevelStrength + $firstLevelAgility) - (1 + 1 + 2),
            $skills->getUnusedFirstLevelPhysicalSkillPointsValue($professionLevels),
            'Expected ' . (($firstLevelStrength + $firstLevelAgility) - (1 + 1 + 2))
        );
        self::assertSame(
            ($nextLevelsStrength + $nextLevelsAgility) - (2 + 3 + 1),
            $skills->getUnusedNextLevelsPhysicalSkillPointsValue($professionLevels),
            'Expected ' . (($nextLevelsStrength + $nextLevelsAgility) - (2 + 3 + 1))
        );
    }

    /**
     * @test
     */
    public function I_can_get_all_fight_with_melee_weapon_skills_at_once(): void
    {
        $expectedFightWithClasses = [
            FightUnarmed::class,
            FightWithAxes::class,
            FightWithKnivesAndDaggers::class,
            FightWithMacesAndClubs::class,
            FightWithMorningstarsAndMorgensterns::class,
            FightWithSabersAndBowieKnives::class,
            FightWithVoulgesAndTridents::class,
            FightWithStaffsAndSpears::class,
            FightWithSwords::class,
            FightWithThrowingWeapons::class,
            FightWithTwoWeapons::class,
            FightWithShields::class,
        ];
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $givenFightWithSkillClasses = [];
        $fightWithSkills = $skills->getFightWithWeaponsUsingPhysicalSkills();
        foreach ($fightWithSkills as $fightWithSkill) {
            self::assertSame(0, $fightWithSkill->getCurrentSkillRank()->getValue());
            $givenFightWithSkillClasses[] = \get_class($fightWithSkill);
        }
        \sort($expectedFightWithClasses);
        \sort($givenFightWithSkillClasses);
        self::assertSame(
            $expectedFightWithClasses,
            $givenFightWithSkillClasses,
            'missing: ' . \implode(',', \array_diff($expectedFightWithClasses, $givenFightWithSkillClasses))
            . "\n" . 'exceeding: ' . \implode(',', \array_diff($givenFightWithSkillClasses, $expectedFightWithClasses))
        );
    }

    /**
     * @test
     * @dataProvider provideWeaponCategories
     * @param string $weaponCategory
     * @param bool $isMelee
     * @param bool $isThrowing
     * @param bool $isShield
     */
    public function I_can_get_malus_for_every_type_of_weaponlike(string $weaponCategory, bool $isMelee, bool $isThrowing, bool $isShield): void
    {
        $weaponlikeCode = $this->createWeaponlikeCode($weaponCategory, $isMelee, $isThrowing, $isShield);
        $this->I_can_get_malus_to_fight_number_with_weaponlike($weaponlikeCode);
        $this->I_can_get_malus_to_attack_number_with_weaponlike($weaponlikeCode);
        if (!$isShield) {
            $weaponCode = $this->createWeaponCode($weaponCategory, $isMelee, $isThrowing);
            $this->I_can_get_malus_to_cover_with_weapon($weaponCode);
        }
        $this->I_can_get_malus_to_base_of_wounds_with_weaponlike($weaponlikeCode);
    }

    private function I_can_get_malus_to_fight_number_with_weaponlike(WeaponlikeCode $weaponlikeCode): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(
            $expectedMalus = 123,
            $skills->getMalusToFightNumberWithWeaponlike(
                $weaponlikeCode,
                $this->createTablesWithMissingWeaponSkillTable('fightNumber', 0 /* expected weapon skill value */, $expectedMalus),
                false // fighting with single weapon only
            )
        );
        $skills->getFightWithTwoWeapons()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        self::assertSame(
            ($expectedWeaponSkillMalus = 456) + ($expectedTwoWeaponsSkillMalus = 789),
            $skills->getMalusToFightNumberWithWeaponlike(
                $weaponlikeCode,
                $this->createTablesWithMissingWeaponSkillTable(
                    'fightNumber',
                    0 /* expected weapon skill value */,
                    $expectedWeaponSkillMalus,
                    $skills->getFightWithTwoWeapons()->getCurrentSkillRank()->getValue(), // expected fight with two weapons skill rank value
                    $expectedTwoWeaponsSkillMalus
                ),
                true // fighting with two weapons
            )
        );
    }

    private function I_can_get_malus_to_attack_number_with_weaponlike(WeaponlikeCode $weaponlikeCode): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(
            $expectedMalus = 456,
            $skills->getMalusToAttackNumberWithWeaponlike(
                $weaponlikeCode,
                $this->createTablesWithMissingWeaponSkillTable('attackNumber', 0 /* expected weapon skill value */, $expectedMalus),
                false // fighting with single weapon only
            )
        );
        $skills->getFightWithTwoWeapons()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        self::assertSame(
            ($expectedWeaponSkillMalus = 567) + ($expectedTwoWeaponsSkillMalus = 891),
            $skills->getMalusToAttackNumberWithWeaponlike(
                $weaponlikeCode,
                $this->createTablesWithMissingWeaponSkillTable(
                    'attackNumber',
                    0 /* expected weapon skill value */,
                    $expectedWeaponSkillMalus,
                    $skills->getFightWithTwoWeapons()->getCurrentSkillRank()->getValue(), // expected fight with two weapons skill rank value
                    $expectedTwoWeaponsSkillMalus
                ),
                true // fighting with two weapons
            )
        );
    }

    private function I_can_get_malus_to_cover_with_weapon(WeaponCode $weaponCode): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(
            $expectedMalus = 789,
            $skills->getMalusToCoverWithWeapon(
                $weaponCode,
                $this->createTablesWithMissingWeaponSkillTable('cover', 0 /* expected weapon skill value */, $expectedMalus),
                false // fighting with single weapon only
            )
        );

        $skills->getFightWithTwoWeapons()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        self::assertSame(
            ($expectedWeaponSkillMalus = 678) + ($expectedTwoWeaponsSkillMalus = 987),
            $skills->getMalusToCoverWithWeapon(
                $weaponCode,
                $this->createTablesWithMissingWeaponSkillTable(
                    'cover',
                    0 /* expected weapon skill value */,
                    $expectedWeaponSkillMalus,
                    $skills->getFightWithTwoWeapons()->getCurrentSkillRank()->getValue(), // expected fight with two weapons skill rank value
                    $expectedTwoWeaponsSkillMalus
                ),
                true // fighting with two weapons
            )
        );
    }

    private function I_can_get_malus_to_base_of_wounds_with_weaponlike(WeaponlikeCode $weaponlikeCode): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(
            $expectedMalus = 101,
            $skills->getMalusToBaseOfWoundsWithWeaponlike(
                $weaponlikeCode,
                $this->createTablesWithMissingWeaponSkillTable('baseOfWounds', 0 /* expected weapon skill value */, $expectedMalus),
                false // fighting with single weapon only
            )
        );
        $skills->getFightWithTwoWeapons()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        self::assertSame(
            ($expectedWeaponSkillMalus = 789) + ($expectedTwoWeaponsSkillMalus = 2223),
            $skills->getMalusToBaseOfWoundsWithWeaponlike(
                $weaponlikeCode,
                $this->createTablesWithMissingWeaponSkillTable(
                    'baseOfWounds',
                    0 /* expected weapon skill value */,
                    $expectedWeaponSkillMalus,
                    $skills->getFightWithTwoWeapons()->getCurrentSkillRank()->getValue(), // expected fight with two weapons skill rank value
                    $expectedTwoWeaponsSkillMalus
                ),
                true // fighting with two weapons
            )
        );
    }

    /**
     * @return array|string[][]
     */
    public function provideWeaponCategories(): array
    {
        return \array_merge(
            \array_map(
                function (string $meleeWeaponCategoryValue) {
                    return [$meleeWeaponCategoryValue, true /* is melee */, false /*  not throwing */, false /* not a shield */];
                },
                WeaponCategoryCode::getMeleeWeaponCategoryValues()
            ),
            [
                // real category names are not required for non-melee weapons (because is{weaponCategory} is not called on them)
                ['foo', false /* not melee */, true /* is throwing */, false /* not a shield */],
                ['bar', true /* is melee */, false /* not throwing */, true /* is shield */],
            ]
        );
    }

    /**
     * @param string $weaponCategory
     * @param bool $isMelee
     * @param bool $isThrowing
     * @param bool $isShield
     * @return \Mockery\MockInterface|WeaponlikeCode
     */
    private function createWeaponlikeCode(string $weaponCategory, bool $isMelee, bool $isThrowing, bool $isShield)
    {
        return $this->createCode(
            $weaponCategory,
            $isMelee,
            $isThrowing,
            false /* not weapon only (wants weaponlike) */,
            $isShield
        );
    }

    /**
     * @param string $weaponCategory
     * @param bool $isMelee
     * @param bool $isThrowing
     * @param bool $isWeaponOnly
     * @param bool $isShield
     * @return \Mockery\MockInterface|WeaponlikeCode|WeaponCode
     * @throws \LogicException
     */
    private function createCode(string $weaponCategory, bool $isMelee, bool $isThrowing, bool $isWeaponOnly, bool $isShield)
    {
        if ($isShield) {
            if ($isThrowing) {
                throw new \LogicException('DO you really want to throw a shield?');
            }
            if ($isWeaponOnly) {
                throw new \LogicException('Shield can not be a weapon (only weapon-like)');
            }
            if (!$isMelee) {
                throw new \LogicException('Why the shield is not a melee?');
            }
        }
        $class = WeaponlikeCode::class;
        if ($isWeaponOnly) {
            $class = WeaponCode::class;
        }
        if ($isMelee) {
            $class = MeleeWeaponCode::class;
        }
        if ($isShield) {
            $class = ShieldCode::class;
        }
        $weaponlikeCode = $this->weakMockery($class);
        $weaponlikeCode->shouldReceive('isMelee')
            ->andReturn($isMelee);
        if ($isMelee) {
            $weaponlikeCode->shouldReceive('convertToMeleeWeaponCodeEquivalent')
                ->andReturn($weaponlikeCode);
        }
        $weaponlikeCode->shouldReceive('isShield')
            ->andReturn($isShield);
        $weaponlikeCode->shouldReceive('isWeapon')
            ->andReturn(\is_a($class, WeaponCode::class, true));
        $weaponlikeCode->shouldReceive('isThrowingWeapon')
            ->andReturn($isThrowing);
        $weaponCodeValue = $weaponCategory;
        if (\strpos($weaponCodeValue, '_and_')) {
            $weaponCodeValue = \explode('_and_', $weaponCodeValue)[0];
        }
        if ($weaponCodeValue === 'knives') {
            $weaponCodeValue = 'knife';
        }
        $weaponCodeValue = \rtrim($weaponCodeValue, 's');
        $isCategory = 'is' . \implode(\array_map(function (string $part) {
                if ($part === 'and') {
                    $part = 'or';
                } elseif ($part === 'knives') {
                    $part = 'knife';
                } else {
                    $part = \rtrim($part, 's');
                }

                return \ucfirst($part);
            }, \explode('_', $weaponCategory)));
        $weaponlikeCode->shouldReceive($isCategory)->andReturn(true);
        $weaponlikeCode->shouldIgnoreMissing(false /* return value for non-mocked methods */);
        $weaponlikeCode->shouldReceive('__toString')
            ->andReturn($weaponCodeValue);

        return $weaponlikeCode;
    }

    /**
     * @param string $weaponCategory
     * @param bool $isMelee
     * @param bool $isThrowing
     * @return \Mockery\MockInterface|WeaponCode
     */
    private function createWeaponCode(string $weaponCategory, bool $isMelee, bool $isThrowing)
    {
        return $this->createCode($weaponCategory, $isMelee, $isThrowing, true /* weapon only */, false /* not a shield */);
    }

    /**
     * @param string $weaponParameterName
     * @param $expectedSkillValue
     * @param $weaponSkillMalus
     * @param int|null $fightsWithTwoWeaponsSkillRankValue
     * @param $fightWithTwoWeaponsSkillMalus
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithMissingWeaponSkillTable(
        $weaponParameterName,
        $expectedSkillValue,
        $weaponSkillMalus,
        $fightsWithTwoWeaponsSkillRankValue = null,
        $fightWithTwoWeaponsSkillMalus = 123
    )
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getMissingWeaponSkillTable')
            ->andReturn($weaponSkillTable = $this->mockery(MissingWeaponSkillTable::class));
        $weaponSkillTable->shouldReceive('get' . \ucfirst($weaponParameterName) . 'MalusForSkillRank')
            ->once()
            ->with($expectedSkillValue)
            ->andReturn($weaponSkillMalus);
        if ($fightsWithTwoWeaponsSkillRankValue) {
            $weaponSkillTable->shouldReceive('get' . \ucfirst($weaponParameterName) . 'MalusForSkillRank')
                ->once()
                ->with($fightsWithTwoWeaponsSkillRankValue)
                ->atLeast()->once()
                ->andReturn($fightWithTwoWeaponsSkillMalus);
        }
        return $tables;
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_cover_with_shield(): void
    {
        $physicalSkills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $tables = $this->createTablesWithShieldUsageSkillTable(0, 123);
        self::assertSame(123, $physicalSkills->getMalusToCoverWithShield($tables));

        $physicalSkills->getShieldUsage()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        $tables = $this->createTablesWithShieldUsageSkillTable(1, 456);
        self::assertSame(456, $physicalSkills->getMalusToCoverWithShield($tables));

        $physicalSkills->getShieldUsage()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        $tables = $this->createTablesWithShieldUsageSkillTable(2, 789);
        self::assertSame(789, $physicalSkills->getMalusToCoverWithShield($tables));

        $physicalSkills->getShieldUsage()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        $tables = $this->createTablesWithShieldUsageSkillTable(3, 101);
        self::assertSame(101, $physicalSkills->getMalusToCoverWithShield($tables));
    }

    /**
     * @param int $expectedPhysicalSkillRankValue
     * @param mixed $coverMalus
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithShieldUsageSkillTable(int $expectedPhysicalSkillRankValue, $coverMalus): Tables
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getShieldUsageSkillTable')
            ->andReturn($shieldUsageSkillTable = $this->mockery(ShieldUsageSkillTable::class));
        $shieldUsageSkillTable->shouldReceive('getCoverMalusForSkillRank')
            ->once()
            ->with(\Mockery::type(PhysicalSkillRank::class))
            ->andReturnUsing(function (PhysicalSkillRank $physicalSkillRank)
            use ($expectedPhysicalSkillRankValue, $coverMalus) {
                self::assertSame($expectedPhysicalSkillRankValue, $physicalSkillRank->getValue());

                return $coverMalus;
            });
        return $tables;
    }

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Physical\Exceptions\PhysicalSkillsDoNotKnowHowToUseThatWeapon
     * @expectedExceptionMessageRegExp ~plank~
     */
    public function I_can_not_get_malus_for_melee_weapon_of_unknown_category(): void
    {
        $physicalSkills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $physicalSkills->getMalusToFightNumberWithWeaponlike(
            $this->createWeaponlikeCode('plank', true /* is melee */, false /* not throwing */, false /* not a shield */),
            Tables::getIt(),
            false // fighting with single weapon only
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Physical\Exceptions\PhysicalSkillsDoNotKnowHowToUseThatWeapon
     * @expectedExceptionMessageRegExp ~artillery~
     */
    public function I_can_not_get_malus_for_non_shield_non_melee_non_throwing_weapon(): void
    {
        $physicalSkills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $physicalSkills->getMalusToFightNumberWithWeaponlike(
            $this->createWeaponlikeCode('artillery', false /* not melee */, false /* not throwing */, false /* not a shield */),
            Tables::getIt(),
            false // fighting with single weapon only
        );
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_fight_for_armor(): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $armourer = $this->createArmourer();

        /** @var BodyArmorCode $bodyArmor */
        $bodyArmor = $this->mockery(BodyArmorCode::class);
        $armourer->shouldReceive('getProtectiveArmamentRestrictionForSkillRank')
            ->once()
            ->andReturnUsing(function (BodyArmorCode $givenBodyArmorCode, PositiveInteger $rank) use ($bodyArmor) {
                self::assertSame($givenBodyArmorCode, $bodyArmor);
                self::assertSame(0, $rank->getValue());
                return 123;
            });
        self::assertSame(123, $skills->getMalusToFightNumberWithProtective($bodyArmor, $armourer));
    }

    /**
     * @return \Mockery\MockInterface|Armourer
     */
    private function createArmourer(): Armourer
    {
        return $this->mockery(Armourer::class);
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_fight_for_helm(): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $armourer = $this->createArmourer();

        /** @var HelmCode $helm */
        $helm = $this->mockery(HelmCode::class);
        $armourer->shouldReceive('getProtectiveArmamentRestrictionForSkillRank')
            ->once()
            ->andReturnUsing(function (HelmCode $givenHelm, PositiveInteger $rank) use ($helm) {
                self::assertSame($givenHelm, $helm);
                self::assertSame(0, $rank->getValue());
                return 456;
            });
        self::assertSame(456, $skills->getMalusToFightNumberWithProtective($helm, $armourer));
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_fight_for_shield(): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $armourer = $this->createArmourer();

        /** @var ShieldCode $shield */
        $shield = $this->mockery(ShieldCode::class);
        $armourer->shouldReceive('getProtectiveArmamentRestrictionForSkillRank')
            ->once()
            ->andReturnUsing(function (ShieldCode $givenShield, PositiveInteger $rank) use ($shield) {
                self::assertSame($givenShield, $shield);
                self::assertSame(0, $rank->getValue());

                return 789;
            });
        self::assertSame(
            789,
            $skills->getMalusToFightNumberWithProtective($shield, $armourer)
        );
    }

    /**
     * @test
     */
    public function I_can_get_maluses_from_ride(): void
    {
        $skills = new PhysicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(-6, $skills->getMalusToFightNumberWhenRiding());
        self::assertSame(-6, $skills->getMalusToAttackNumberWhenRiding());
        self::assertSame(-6, $skills->getMalusToDefenseNumberWhenRiding());

        $professionFirstLevel = $this->createProfessionFirstLevel();
        $skills->getRiding()->increaseSkillRank($this->createSkillPoint($professionFirstLevel));
        self::assertSame(-4, $skills->getMalusToFightNumberWhenRiding());
        self::assertSame(-4, $skills->getMalusToAttackNumberWhenRiding());
        self::assertSame(-4, $skills->getMalusToDefenseNumberWhenRiding());

        $professionNextLevel = $this->createProfessionNextLevel();
        $skills->getRiding()->increaseSkillRank($this->createSkillPoint($professionNextLevel));
        self::assertSame(-2, $skills->getMalusToFightNumberWhenRiding());
        self::assertSame(-2, $skills->getMalusToAttackNumberWhenRiding());
        self::assertSame(-2, $skills->getMalusToDefenseNumberWhenRiding());

        $professionNextLevel = $this->createProfessionNextLevel();
        $skills->getRiding()->increaseSkillRank($this->createSkillPoint($professionNextLevel));
        self::assertSame(0, $skills->getMalusToFightNumberWhenRiding());
        self::assertSame(0, $skills->getMalusToAttackNumberWhenRiding());
        self::assertSame(0, $skills->getMalusToDefenseNumberWhenRiding());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Physical\Exceptions\PhysicalSkillsDoNotKnowHowToUseThatArmament
     */
    public function I_do_not_get_malus_to_fight_for_unknown_protective(): void
    {
        /** @var ProtectiveArmamentCode $protectiveArmamentCode */
        $protectiveArmamentCode = $this->mockery(ProtectiveArmamentCode::class);
        (new PhysicalSkills(ProfessionFirstLevel::createFirstLevel(Commoner::getIt())))->getMalusToFightNumberWithProtective(
            $protectiveArmamentCode,
            $this->createArmourer()
        );
    }
}