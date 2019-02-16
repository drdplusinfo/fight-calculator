<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Skills\SkillPointTest;

class PhysicalSkillPointTest extends SkillPointTest
{
    protected function I_can_create_skill_point_by_first_level_background_skills()
    {
        $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
            $level = $this->createProfessionFirstLevel(ProfessionCode::FIGHTER),
            $skillsFromBackground = $this->createSkillPointsFromBackground(123, 'getPhysicalSkillPoints'),
            Tables::getIt()
        );
        self::assertInstanceOf(PhysicalSkillPoint::class, $physicalSkillPoint);
        self::assertSame(1, $physicalSkillPoint->getValue());
        self::assertSame('physical', $physicalSkillPoint->getTypeName());
        self::assertSame([PropertyCode::STRENGTH, PropertyCode::AGILITY], $physicalSkillPoint->getRelatedProperties());
        self::assertSame($skillsFromBackground, $physicalSkillPoint->getSkillPointsFromBackground());
        self::assertNull($physicalSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertNull($physicalSkillPoint->getSecondPaidOtherSkillPoint());

        return [$physicalSkillPoint, $level];
    }

    protected function I_can_create_skill_point_by_cross_type_skill_points()
    {
        $skillPointsAndLevels = [];
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_from_two_combined_skill_points();
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_from_two_psychical_skill_points();
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_from_psychical_and_combined_skill_points();

        return $skillPointsAndLevels;
    }

    private function I_can_create_skill_point_from_two_combined_skill_points()
    {
        $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelCrossTypeSkillPoints(
            $level = $this->createProfessionFirstLevel(),
            $firstPaidSkillPoint = $this->createCombinedSkillPoint(),
            $secondPaidSkillPoint = $this->createCombinedSkillPoint(),
            Tables::getIt()
        );
        self::assertInstanceOf(PhysicalSkillPoint::class, $physicalSkillPoint);
        self::assertNull($physicalSkillPoint->getSkillPointsFromBackground());
        self::assertSame($firstPaidSkillPoint, $physicalSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertSame($secondPaidSkillPoint, $physicalSkillPoint->getSecondPaidOtherSkillPoint());

        return [$physicalSkillPoint, $level];
    }

    private function I_can_create_skill_point_from_two_psychical_skill_points()
    {
        $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelCrossTypeSkillPoints(
            $level = $this->createProfessionFirstLevel(),
            $firstPaidSkillPoint = $this->createPsychicalSkillPoint(),
            $secondPaidSkillPoint = $this->createPsychicalSkillPoint(),
            Tables::getIt()
        );
        self::assertInstanceOf(PhysicalSkillPoint::class, $physicalSkillPoint);
        self::assertNull($physicalSkillPoint->getSkillPointsFromBackground());
        self::assertSame($firstPaidSkillPoint, $physicalSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertSame($secondPaidSkillPoint, $physicalSkillPoint->getSecondPaidOtherSkillPoint());

        return [$physicalSkillPoint, $level];
    }

    private function I_can_create_skill_point_from_psychical_and_combined_skill_points()
    {
        $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelCrossTypeSkillPoints(
            $level = $this->createProfessionFirstLevel(),
            $firstPaidSkillPoint = $this->createPsychicalSkillPoint(),
            $secondPaidSkillPoint = $this->createCombinedSkillPoint(),
            Tables::getIt()
        );
        self::assertInstanceOf(PhysicalSkillPoint::class, $physicalSkillPoint);
        self::assertNull($physicalSkillPoint->getSkillPointsFromBackground());
        self::assertSame($firstPaidSkillPoint, $physicalSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertSame($secondPaidSkillPoint, $physicalSkillPoint->getSecondPaidOtherSkillPoint());

        return [$physicalSkillPoint, $level];
    }

    protected function I_can_create_skill_point_by_related_property_increase()
    {
        $skillPointsAndLevels = [];
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_by_level_by_strength_adjustment();
        $skillPointsAndLevels[] = $this->I_can_create_skill_point_by_level_by_agility_adjustment();

        return $skillPointsAndLevels;
    }

    private function I_can_create_skill_point_by_level_by_strength_adjustment()
    {
        $physicalSkillPoint = PhysicalSkillPoint::createFromNextLevelPropertyIncrease(
            $level = $this->createProfessionNextLevel(Agility::class, Strength::class),
            Tables::getIt()
        );
        self::assertInstanceOf(PhysicalSkillPoint::class, $physicalSkillPoint);
        self::assertNull($physicalSkillPoint->getSkillPointsFromBackground());
        self::assertNull($physicalSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertNull($physicalSkillPoint->getSecondPaidOtherSkillPoint());

        return [$physicalSkillPoint, $level];
    }

    private function I_can_create_skill_point_by_level_by_agility_adjustment()
    {
        $physicalSkillPoint = PhysicalSkillPoint::createFromNextLevelPropertyIncrease(
            $level = $this->createProfessionNextLevel(Strength::class, Agility::class),
            Tables::getIt()
        );
        self::assertInstanceOf(PhysicalSkillPoint::class, $physicalSkillPoint);
        self::assertNull($physicalSkillPoint->getSkillPointsFromBackground());
        self::assertNull($physicalSkillPoint->getFirstPaidOtherSkillPoint());
        self::assertNull($physicalSkillPoint->getSecondPaidOtherSkillPoint());

        return [$physicalSkillPoint, $level];
    }

}