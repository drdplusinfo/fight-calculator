<?php declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Codes\Skills\SkillCode;

trait UsingSkills
{

    /**
     * @param string|null $skillName
     * @param SkillCode $defaultSkillCode
     * @return SkillCode
     * @throws \DrdPlus\FightCalculator\Exceptions\UnknownSkill
     */
    private function getSkill(?string $skillName, SkillCode $defaultSkillCode): SkillCode
    {
        if ($skillName === null) {
            return $defaultSkillCode;
        }

        if (\in_array($skillName, PhysicalSkillCode::getPossibleValues(), true)) {
            return PhysicalSkillCode::getIt($skillName);
        }

        if (\in_array($skillName, PsychicalSkillCode::getPossibleValues(), true)) {
            return PsychicalSkillCode::getIt($skillName);
        }
        if (\in_array($skillName, CombinedSkillCode::getPossibleValues(), true)) {
            return CombinedSkillCode::getIt($skillName);
        }

        throw new Exceptions\UnknownSkill('Given skill is unknown: ' . \var_export($skillName, true));
    }

}