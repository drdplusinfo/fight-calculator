<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Skills\SkillPointTest;

class CombinedSkillPointTest extends SkillPointTest
{
    protected function I_can_create_skill_point_by_first_level_background_skills()
    {
        $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelSkillPointsFromBackground(
            $level = $this->createProfessionFirstLevel(ProfessionCode::FIGHTER),
            $skillsFromBackground = $this->createSkillPointsFromBackground(123, 'getCombinedSkillPoints'),
            Tables::getIt()
        );
        self::assertInstanceOf(CombinedSkillPoint::class, $combinedSkillPoint);
        self::assertSame(1, $combinedSkillPoint->getValue());
        self::assertSame('combined', $combinedSkillPoint->getTypeName());
        self::assertSame([PropertyCode::KNACK, PropertyCode::CHARISMA], $combinedSkillPoint->getRelatedProperties());
        self::assertSame($skillsFromBackground, $combinedSkillPoint->getSkillPointsFromBackground());
        self::assertNull($combinedSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertNull($combinedSkillPoint->getSecondPaidOtherSkillPoint());

        return [$combinedSkillPoint, $level];
    }

    protected function I_can_create_skill_point_by_cross_type_skill_points()
    {
        $skillPointsAndLevels = [];
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_from_two_physical_skill_points();
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_from_two_psychical_skill_points();
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_from_psychical_and_physical_skill_points();

        return $skillPointsAndLevels;
    }

    private function I_can_create_skill_point_from_two_physical_skill_points()
    {
        $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelCrossTypeSkillPoints(
            $level = $this->createProfessionFirstLevel(),
            $firstPaidSkillPoint = $this->createPhysicalSkillPoint(),
            $secondPaidSkillPoint = $this->createPhysicalSkillPoint(),
            Tables::getIt()
        );
        self::assertInstanceOf(CombinedSkillPoint::class, $combinedSkillPoint);
        self::assertNull($combinedSkillPoint->getSkillPointsFromBackground());
        self::assertSame($firstPaidSkillPoint, $combinedSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertSame($secondPaidSkillPoint, $combinedSkillPoint->getSecondPaidOtherSkillPoint());

        return [$combinedSkillPoint, $level];
    }

    private function I_can_create_skill_point_from_two_psychical_skill_points()
    {
        $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelCrossTypeSkillPoints(
            $level = $this->createProfessionFirstLevel(),
            $firstPaidSkillPoint = $this->createPsychicalSkillPoint(),
            $secondPaidSkillPoint = $this->createPsychicalSkillPoint(),
            Tables::getIt()
        );
        self::assertInstanceOf(CombinedSkillPoint::class, $combinedSkillPoint);
        self::assertNull($combinedSkillPoint->getSkillPointsFromBackground());
        self::assertSame($firstPaidSkillPoint, $combinedSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertSame($secondPaidSkillPoint, $combinedSkillPoint->getSecondPaidOtherSkillPoint());

        return [$combinedSkillPoint, $level];
    }

    private function I_can_create_skill_point_from_psychical_and_physical_skill_points()
    {
        $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelCrossTypeSkillPoints(
            $level = $this->createProfessionFirstLevel(),
            $firstPaidSkillPoint = $this->createPsychicalSkillPoint(),
            $secondPaidSkillPoint = $this->createPhysicalSkillPoint(),
            Tables::getIt()
        );
        self::assertInstanceOf(CombinedSkillPoint::class, $combinedSkillPoint);
        self::assertNull($combinedSkillPoint->getSkillPointsFromBackground());
        self::assertSame($firstPaidSkillPoint, $combinedSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertSame($secondPaidSkillPoint, $combinedSkillPoint->getSecondPaidOtherSkillPoint());

        return [$combinedSkillPoint, $level];
    }

    protected function I_can_create_skill_point_by_related_property_increase()
    {
        $skillPointsAndLevels = [];
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_by_level_knack_adjustment();
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_by_level_charisma_adjustment();

        return $skillPointsAndLevels;
    }

    private function I_can_create_skill_point_by_level_knack_adjustment()
    {
        $combinedSkillPoint = CombinedSkillPoint::createFromNextLevelPropertyIncrease(
            $level = $this->createProfessionNextLevel(Knack::class, Charisma::class),
            Tables::getIt()
        );
        self::assertInstanceOf(CombinedSkillPoint::class, $combinedSkillPoint);
        self::assertNull($combinedSkillPoint->getSkillPointsFromBackground());
        self::assertNull($combinedSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertNull($combinedSkillPoint->getSecondPaidOtherSkillPoint());

        return [$combinedSkillPoint, $level];
    }

    private function I_can_create_skill_point_by_level_charisma_adjustment()
    {
        $combinedSkillPoint = CombinedSkillPoint::createFromNextLevelPropertyIncrease(
            $level = $this->createProfessionNextLevel(Charisma::class, Knack::class),
            Tables::getIt()
        );
        self::assertInstanceOf(CombinedSkillPoint::class, $combinedSkillPoint);
        self::assertNull($combinedSkillPoint->getSkillPointsFromBackground());
        self::assertNull($combinedSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertNull($combinedSkillPoint->getSecondPaidOtherSkillPoint());

        return [$combinedSkillPoint, $level];
    }

}