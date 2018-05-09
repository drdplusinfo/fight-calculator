<?php
declare(strict_types=1);
/** be strict for parameter types, https://www.quora.com/Are-strict_types-in-PHP-7-not-a-bad-idea */

namespace DrdPlus\Calculator\Fight;

use DrdPlus\Calculator\AttackSkeleton\PreviousArmaments;
use DrdPlus\Codes\Skills\SkillCode;

class PreviousArmamentsWithSkills extends PreviousArmaments
{
    use UsingSkills;

    public function getPreviousFightWithShieldsSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(Controller::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    public function getPreviousShieldUsageSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(Controller::SHIELD_USAGE_SKILL_RANK);
    }

    public function getPreviousArmorSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(Controller::ARMOR_SKILL_VALUE);
    }

    public function getPreviousOnHorseback(): bool
    {
        return (bool)$this->getHistory()->getValue(Controller::ON_HORSEBACK);
    }

    public function getPreviousRidingSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(Controller::RIDING_SKILL_RANK);
    }

    public function getPreviousFightFreeWillAnimal(): bool
    {
        return (bool)$this->getHistory()->getValue(Controller::FIGHT_FREE_WILL_ANIMAL);
    }

    public function getPreviousZoologySkillRank(): int
    {
        return (int)$this->getHistory()->getValue(Controller::ZOOLOGY_SKILL_RANK);
    }

    /**
     * @return SkillCode
     * @throws \DrdPlus\Calculator\Fight\Exceptions\UnknownSkill
     */
    public function getPreviousRangedSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->getHistory()->getValue(Controller::RANGED_FIGHT_SKILL));
    }

    public function getPreviousRangedSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(Controller::RANGED_FIGHT_SKILL_RANK);
    }

}