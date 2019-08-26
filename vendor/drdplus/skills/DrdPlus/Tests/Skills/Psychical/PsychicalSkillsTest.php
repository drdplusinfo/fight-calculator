<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Psychical;

use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Professions\Commoner;
use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Skills\Psychical\PsychicalSkills;
use DrdPlus\Tests\Skills\SameTypeSkillsTest;

class PsychicalSkillsTest extends SameTypeSkillsTest
{

    /**
     * @test
     */
    public function I_can_get_unused_skill_points_from_first_level()
    {
        $skills = new PsychicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $professionLevels = $this->createProfessionLevels(
            $firstLevelWill = 123, $firstLevelIntelligence = 456, $nextLevelWill = 321, $nextLevelIntelligence = 654
        );
        self::assertSame(
            $firstLevelWill + $firstLevelIntelligence,
            $skills->getUnusedFirstLevelPsychicalSkillPointsValue($professionLevels)
        );

        $skills->getAstronomy()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel())); // 1
        $skills->getAstronomy()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel())); // 2
        $skills->getAstronomy()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel())); // 3
        $skills->getBotany()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel())); // 1
        $skills->getBotany()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel())); // 2
        self::assertSame(
            ($firstLevelWill + $firstLevelIntelligence) - (1 + 2 + 3 + 1 + 2),
            $skills->getUnusedFirstLevelPsychicalSkillPointsValue($professionLevels),
            'Expected ' . (($firstLevelWill + $firstLevelIntelligence) - (1 + 2 + 3 + 1 + 2))
        );
    }

    /**
     * @param int $firstLevelWillModifier
     * @param int $firstLevelIntelligenceModifier
     * @param int $nextLevelsWillModifier
     * @param int $nextLevelsIntelligenceModifier
     * @return \Mockery\MockInterface|ProfessionLevels
     */
    private function createProfessionLevels(
        $firstLevelWillModifier, $firstLevelIntelligenceModifier, $nextLevelsWillModifier, $nextLevelsIntelligenceModifier
    )
    {
        $professionLevels = $this->mockery(ProfessionLevels::class);
        $professionLevels->shouldReceive('getFirstLevelWillModifier')
            ->andReturn($firstLevelWillModifier);
        $professionLevels->shouldReceive('getFirstLevelIntelligenceModifier')
            ->andReturn($firstLevelIntelligenceModifier);
        $professionLevels->shouldReceive('getNextLevelsWillModifier')
            ->andReturn($nextLevelsWillModifier);
        $professionLevels->shouldReceive('getNextLevelsIntelligenceModifier')
            ->andReturn($nextLevelsIntelligenceModifier);

        return $professionLevels;
    }

    /**
     * @test
     */
    public function I_can_not_increase_rank_by_zero_skill_point()
    {
        $this->expectException(\DrdPlus\Skills\Exceptions\CanNotUseZeroSkillPointForNonZeroSkillRank::class);
        $this->expectExceptionMessageRegExp('~0~');
        $skills = new PsychicalSkills($professionZeroLevel = ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $skills->getEtiquetteOfGangland()->increaseSkillRank(PsychicalSkillPoint::createZeroSkillPoint($professionZeroLevel));
    }

    /**
     * @test
     */
    public function I_can_get_unused_skill_points_from_next_levels()
    {
        $skills = new PsychicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $professionLevels = $this->createProfessionLevels(
            $firstLevelWill = 123, $firstLevelIntelligence = 456, $nextLevelsWill = 321, $nextLevelsIntelligence = 654
        );
        self::assertSame($nextLevelsWill + $nextLevelsIntelligence, $skills->getUnusedNextLevelsPsychicalSkillPointsValue($professionLevels));

        $skills->getMythology()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel())); // 1 - first level
        $skills->getMythology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel())); // 2 - next level
        self::assertSame(
            $firstLevelWill + $firstLevelIntelligence - 1,
            $skills->getUnusedFirstLevelPsychicalSkillPointsValue($professionLevels)
        );
        self::assertSame(
            $nextLevelsWill + $nextLevelsIntelligence - 2,
            $skills->getUnusedNextLevelsPsychicalSkillPointsValue($professionLevels)
        );

        $skills->getTechnology()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel())); // 1 - first level
        $skills->getTechnology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel())); // 2 - next level
        $skills->getTechnology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel())); // 3 - next level
        $skills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel())); // 1 - next level
        self::assertSame(
            ($firstLevelWill + $firstLevelIntelligence) - (1 + 1),
            $skills->getUnusedFirstLevelPsychicalSkillPointsValue($professionLevels),
            'Expected ' . (($firstLevelWill + $firstLevelIntelligence) - (1 + 1))
        );
        self::assertSame(
            ($nextLevelsWill + $nextLevelsIntelligence) - (2 + 2 + 3 + 1),
            $skills->getUnusedNextLevelsPsychicalSkillPointsValue($professionLevels),
            'Expected ' . (($nextLevelsWill + $nextLevelsIntelligence) - (2 + 2 + 3 + 1))
        );
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_attack_number_against_free_will_animal()
    {
        $physicalSkills = new PsychicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(0, $physicalSkills->getBonusToAttackNumberAgainstFreeWillAnimal());
        $physicalSkills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        self::assertSame(
            $physicalSkills->getZoology()->getBonusToAttackNumberAgainstFreeWillAnimal(),
            $physicalSkills->getBonusToAttackNumberAgainstFreeWillAnimal()
        );
        $physicalSkills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel()));
        self::assertSame(
            $physicalSkills->getZoology()->getBonusToAttackNumberAgainstFreeWillAnimal(),
            $physicalSkills->getBonusToAttackNumberAgainstFreeWillAnimal()
        );
        $physicalSkills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel()));
        self::assertSame(
            $physicalSkills->getZoology()->getBonusToAttackNumberAgainstFreeWillAnimal(),
            $physicalSkills->getBonusToAttackNumberAgainstFreeWillAnimal()
        );
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_cover_against_free_will_animal()
    {
        $physicalSkills = new PsychicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(0, $physicalSkills->getBonusToCoverAgainstFreeWillAnimal());
        $physicalSkills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        self::assertSame(
            $physicalSkills->getZoology()->getBonusToCoverAgainstFreeWillAnimal(),
            $physicalSkills->getBonusToCoverAgainstFreeWillAnimal()
        );
        $physicalSkills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel()));
        self::assertSame(
            $physicalSkills->getZoology()->getBonusToCoverAgainstFreeWillAnimal(),
            $physicalSkills->getBonusToCoverAgainstFreeWillAnimal()
        );
        $physicalSkills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel()));
        self::assertSame(
            $physicalSkills->getZoology()->getBonusToCoverAgainstFreeWillAnimal(),
            $physicalSkills->getBonusToCoverAgainstFreeWillAnimal()
        );
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_base_of_wounds_against_free_will_animal()
    {
        $physicalSkills = new PsychicalSkills(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(0, $physicalSkills->getBonusToBaseOfWoundsAgainstFreeWillAnimal());
        $physicalSkills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        self::assertSame(
            $physicalSkills->getZoology()->getBonusToBaseOfWoundsAgainstFreeWillAnimal(),
            $physicalSkills->getBonusToBaseOfWoundsAgainstFreeWillAnimal()
        );
        $physicalSkills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel()));
        self::assertSame(
            $physicalSkills->getZoology()->getBonusToBaseOfWoundsAgainstFreeWillAnimal(),
            $physicalSkills->getBonusToBaseOfWoundsAgainstFreeWillAnimal()
        );
        $physicalSkills->getZoology()->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel()));
        self::assertSame(
            $physicalSkills->getZoology()->getBonusToBaseOfWoundsAgainstFreeWillAnimal(),
            $physicalSkills->getBonusToBaseOfWoundsAgainstFreeWillAnimal()
        );
    }
}