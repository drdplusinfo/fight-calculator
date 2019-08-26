<?php declare(strict_types=1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Professions\Commoner;
use DrdPlus\Professions\Fighter;
use DrdPlus\Professions\Wizard;
use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\Combined\CombinedSkills;
use DrdPlus\Tables\Armaments\Weapons\MissingWeaponSkillTable;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Skills\SameTypeSkillsTest;

class CombinedSkillsTest extends SameTypeSkillsTest
{

    /**
     * @test
     */
    public function I_can_get_unused_skill_points_from_first_level(): void
    {
        $skills = new CombinedSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $professionLevels = $this->createProfessionLevels(
            $firstLevelKnack = 123,
            $firstLevelCharisma = 456,
            $nextLevelKnack = 321,
            $nextLevelCharisma = 654
        );

        self::assertSame(
            $firstLevelKnack + $firstLevelCharisma,
            $skills->getUnusedFirstLevelCombinedSkillPointsValue($professionLevels)
        );

        $professionFirstLevel = ProfessionFirstLevel::createFirstLevel(Fighter::getIt());
        $skills->getBigHandwork()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // = 1
        $skills->getBigHandwork()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // = 2
        $skills->getCooking()->increaseSkillRank($this->createSkillPoint($professionFirstLevel)); // = 1
        self::assertSame(
            ($firstLevelKnack + $firstLevelCharisma) - 4 /* 1 + 2 +1 */,
            $skills->getUnusedFirstLevelCombinedSkillPointsValue($professionLevels),
            'Expected ' . (($firstLevelKnack + $firstLevelCharisma) - 4 /* 1 + 2 + 1 */)
        );
    }

    /**
     * @param int $firstLevelKnackModifier
     * @param int $firstLevelCharismaModifier
     * @param int $nextLevelsKnackModifier
     * @param int $nextLevelsCharismaModifier
     * @return \Mockery\MockInterface|ProfessionLevels
     */
    private function createProfessionLevels(
        $firstLevelKnackModifier,
        $firstLevelCharismaModifier,
        $nextLevelsKnackModifier,
        $nextLevelsCharismaModifier
    ): ProfessionLevels
    {
        $professionLevels = $this->mockery(ProfessionLevels::class);
        $professionLevels->shouldReceive('getFirstLevelKnackModifier')
            ->andReturn($firstLevelKnackModifier);
        $professionLevels->shouldReceive('getFirstLevelCharismaModifier')
            ->andReturn($firstLevelCharismaModifier);
        $professionLevels->shouldReceive('getNextLevelsKnackModifier')
            ->andReturn($nextLevelsKnackModifier);
        $professionLevels->shouldReceive('getNextLevelsCharismaModifier')
            ->andReturn($nextLevelsCharismaModifier);

        return $professionLevels;
    }

    /**
     * @test
     */
    public function I_can_not_increase_rank_by_zero_skill_point(): void
    {
        $this->expectException(\DrdPlus\Skills\Exceptions\CanNotUseZeroSkillPointForNonZeroSkillRank::class);
        $this->expectExceptionMessageRegExp('~0~');
        $skills = new CombinedSkills($professionZeroLevel = ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $skills->getCooking()->increaseSkillRank(CombinedSkillPoint::createZeroSkillPoint($professionZeroLevel));
    }

    /**
     * @test
     */
    public function I_can_get_unused_skill_points_from_next_levels(): void
    {
        $skills = new CombinedSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $professionLevels = $this->createProfessionLevels(
            $firstLevelKnack = 123,
            $firstLevelCharisma = 456,
            $nextLevelsKnack = 321,
            $nextLevelsCharisma = 654
        );

        self::assertSame(
            $nextLevelsKnack + $nextLevelsCharisma,
            $skills->getUnusedNextLevelsCombinedSkillPointsValue($professionLevels)
        );
        $skills->getFirstAid()->increaseSkillRank( // = 1
            $this->createSkillPoint(ProfessionFirstLevel::createFirstLevel(Wizard::getIt()))
        );
        $skills->getFirstAid()->increaseSkillRank( // = 2
            $this->createSkillPoint(ProfessionFirstLevel::createFirstLevel(Wizard::getIt()))
        );
        self::assertSame(
            $nextLevelsKnack + $nextLevelsCharisma,
            $skills->getUnusedNextLevelsCombinedSkillPointsValue($professionLevels),
            'Nothing should change'
        );

        $skills->getGambling()->increaseSkillRank( // 1
            $this->createSkillPoint($this->createProfessionNextLevel())
        );
        $skills->getSeduction()->increaseSkillRank( // 1
            $this->createSkillPoint($this->createProfessionNextLevel())
        );
        $skills->getSeduction()->increaseSkillRank( // 1
            $this->createSkillPoint($this->createProfessionNextLevel())
        );
        self::assertSame(
            ($nextLevelsKnack + $nextLevelsCharisma) - (1 + 1 + 2),
            $skills->getUnusedNextLevelsCombinedSkillPointsValue($professionLevels),
            'Expected ' . (($nextLevelsKnack + $nextLevelsCharisma) - (1 + 1 + 2))
        );
    }

    /**
     * @test
     * @dataProvider provideWeaponCategories
     * @param string $rangeWeaponCategory
     */
    public function I_can_get_malus_for_every_type_of_weapon(string $rangeWeaponCategory): void
    {
        $combinedSkills = new CombinedSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(
            $expectedMalus = 123,
            $combinedSkills->getMalusToFightNumberWithShootingWeapon(
                $this->createRangeWeaponCode($rangeWeaponCategory),
                $this->createTablesWithMissingWeaponSkillTable('fightNumber', 0 /* expected skill value */, $expectedMalus)
            )
        );
        self::assertSame(
            $expectedMalus = 456,
            $combinedSkills->getMalusToAttackNumberWithShootingWeapon(
                $this->createRangeWeaponCode($rangeWeaponCategory),
                $this->createTablesWithMissingWeaponSkillTable('attackNumber', 0 /* expected skill value */, $expectedMalus)
            )
        );
        self::assertSame(
            $expectedMalus = 789,
            $combinedSkills->getMalusToCoverWithShootingWeapon(
                $this->createRangeWeaponCode($rangeWeaponCategory),
                $this->createTablesWithMissingWeaponSkillTable('cover', 0 /* expected skill value */, $expectedMalus)
            )
        );
        self::assertSame(
            $expectedMalus = 101,
            $combinedSkills->getMalusToBaseOfWoundsWithShootingWeapon(
                $this->createRangeWeaponCode($rangeWeaponCategory),
                $this->createTablesWithMissingWeaponSkillTable('baseOfWounds', 0 /* expected skill value */, $expectedMalus)
            )
        );
    }

    /**
     * @return array|string[][]
     */
    public function provideWeaponCategories(): array
    {
        return [
            [WeaponCategoryCode::BOWS],
            [WeaponCategoryCode::CROSSBOWS],
        ];
    }

    /**
     * @param string $weaponCategory
     * @return \Mockery\MockInterface|RangedWeaponCode
     */
    private function createRangeWeaponCode(string $weaponCategory): RangedWeaponCode
    {
        $weaponCodeValue = \rtrim($weaponCategory, 's');
        $code = $this->weakMockery(RangedWeaponCode::class); // without check if mocked method exists
        $code->shouldReceive('is' . \ucfirst($weaponCodeValue))
            ->andReturn('true');
        $code->shouldIgnoreMissing(false /* return value for non-mocked methods */);
        $code->shouldReceive('__toString')
            ->andReturn($weaponCodeValue);

        return $code;
    }

    /**
     * @param string $weaponParameterName
     * @param $expectedSkillValue
     * @param $result
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithMissingWeaponSkillTable($weaponParameterName, $expectedSkillValue, $result): Tables
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getMissingWeaponSkillTable')
            ->andReturn($missingWeaponSkillsTable = $this->mockery(MissingWeaponSkillTable::class));
        $missingWeaponSkillsTable->shouldReceive('get' . \ucfirst($weaponParameterName) . 'MalusForSkillRank')
            ->once()
            ->with($expectedSkillValue)
            ->andReturn($result);

        return $tables;
    }

    /**
     * @test
     */
    public function I_can_not_get_malus_for_weapon_not_affected_by_combined_skill(): void
    {
        $this->expectException(\DrdPlus\Skills\Combined\Exceptions\CombinedSkillsDoNotHowToUseThatWeapon::class);
        $this->expectExceptionMessageRegExp('~notBowNorCrossbowYouKnow~');
        $combinedSkills = new CombinedSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $combinedSkills->getMalusToFightNumberWithShootingWeapon(
            $this->createRangeWeaponCode('notBowNorCrossbowYouKnow'),
            Tables::getIt()
        );
    }

}