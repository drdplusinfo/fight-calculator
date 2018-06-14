<?php
declare(strict_types=1);
/** be strict for parameter types, https://www.quora.com/Are-strict_types-in-PHP-7-not-a-bad-idea */

namespace DrdPlus\FightCalculator;

use DrdPlus\AttackSkeleton\PreviousArmaments;
use DrdPlus\Codes\Skills\SkillCode;

class PreviousArmamentsWithSkills extends PreviousArmaments
{
    use UsingSkills;

    public function getPreviousFightWithShieldsSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightController::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    public function getPreviousShieldUsageSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightController::SHIELD_USAGE_SKILL_RANK);
    }

    public function getPreviousArmorSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightController::ARMOR_SKILL_VALUE);
    }

    public function getPreviousOnHorseback(): bool
    {
        return (bool)$this->getHistory()->getValue(FightController::ON_HORSEBACK);
    }

    public function getPreviousRidingSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightController::RIDING_SKILL_RANK);
    }

    public function getPreviousFightFreeWillAnimal(): bool
    {
        return (bool)$this->getHistory()->getValue(FightController::FIGHT_FREE_WILL_ANIMAL);
    }

    public function getPreviousZoologySkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightController::ZOOLOGY_SKILL_RANK);
    }

    /**
     * @return SkillCode
     * @throws \DrdPlus\FightCalculator\Exceptions\UnknownSkill
     */
    public function getPreviousRangedSkillCode(): SkillCode
    {
        return $this->getCurrentSkill($this->getHistory()->getValue(FightController::RANGED_FIGHT_SKILL));
    }

    public function getPreviousRangedSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(FightController::RANGED_FIGHT_SKILL_RANK);
    }

}