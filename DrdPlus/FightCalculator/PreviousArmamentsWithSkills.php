<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\AttackSkeleton\PreviousArmaments;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Codes\Skills\SkillCode;

class PreviousArmamentsWithSkills extends PreviousArmaments
{
    use UsingSkills;

    public function getPreviousFightWithShieldsSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightRequest::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    public function getPreviousShieldUsageSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightRequest::SHIELD_USAGE_SKILL_RANK);
    }

    public function getPreviousArmorSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightRequest::ARMOR_SKILL_VALUE);
    }

    public function getPreviousOnHorseback(): bool
    {
        return (bool)$this->getHistory()->getValue(FightRequest::ON_HORSEBACK);
    }

    public function getPreviousRidingSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightRequest::RIDING_SKILL_RANK);
    }

    public function getPreviousFightingFreeWillAnimal(): bool
    {
        return (bool)$this->getHistory()->getValue(FightRequest::FIGHTING_FREE_WILL_ANIMAL);
    }

    public function getPreviousZoologySkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightRequest::ZOOLOGY_SKILL_RANK);
    }

    public function getPreviousRangedFightSkillCode(): SkillCode
    {
        return $this->getSkill(
            $this->getHistory()->getValue(FightRequest::RANGED_FIGHT_SKILL),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_UNARMED)
        );
    }

    public function getPreviousRangedFightSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightRequest::RANGED_FIGHT_SKILL_RANK);
    }

    public function getPreviousProfessionCode(): ProfessionCode
    {
        $previousProfessionValue = $this->getHistory()->getValue(FightRequest::PROFESSION);
        if (!$previousProfessionValue) {
            $previousProfessionValue = ProfessionCode::COMMONER;
        }
        return ProfessionCode::getIt($previousProfessionValue);
    }
}