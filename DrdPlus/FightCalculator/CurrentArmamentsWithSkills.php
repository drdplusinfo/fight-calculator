<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CustomArmamentsRegistrar;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Codes\Skills\SkillCode;

class CurrentArmamentsWithSkills extends CurrentArmaments
{
    use UsingSkills;

    /** @var CurrentValues */
    private $currentValues;

    public function __construct(
        CurrentProperties $currentProperties,
        CurrentArmamentsValues $currentArmamentsValues,
        Armourer $armourer,
        CustomArmamentsRegistrar $customArmamentsRegistrar,
        CurrentValues $currentValues
    )
    {
        parent::__construct($currentProperties, $currentArmamentsValues, $armourer, $customArmamentsRegistrar);
        $this->currentValues = $currentValues;
    }

    public function getCurrentMeleeFightSkillCode(): SkillCode
    {
        return $this->getSkill(
            $this->currentValues->getCurrentValue(FightRequest::MELEE_FIGHT_SKILL),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_UNARMED)
        );
    }

    public function getCurrentMeleeFightSkillRank(): int
    {
        return (int)$this->currentValues->getCurrentValue(FightRequest::MELEE_FIGHT_SKILL_RANK);
    }

    /**
     * @return SkillCode
     * @throws \DrdPlus\FightCalculator\Exceptions\UnknownSkill
     */
    public function getCurrentRangedFightSkillCode(): SkillCode
    {
        return $this->getSkill(
            $this->currentValues->getCurrentValue(FightRequest::RANGED_FIGHT_SKILL),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_UNARMED)
        );
    }

    public function getCurrentRangedFightSkillRank(): int
    {
        return (int)$this->currentValues->getCurrentValue(FightRequest::RANGED_FIGHT_SKILL_RANK);
    }

    public function getCurrentShieldUsageSkillRank(): int
    {
        return (int)$this->currentValues->getCurrentValue(FightRequest::SHIELD_USAGE_SKILL_RANK);
    }

    public function getCurrentFightWithShieldsSkillRank(): int
    {
        return (int)$this->currentValues->getCurrentValue(FightRequest::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    public function getCurrentArmorSkillRank(): int
    {
        return (int)$this->currentValues->getCurrentValue(FightRequest::ARMOR_SKILL_VALUE);
    }

    public function getCurrentProfessionCode(): ProfessionCode
    {
        $selectedProfession = $this->currentValues->getCurrentValue(FightRequest::PROFESSION);
        if (!$selectedProfession) {
            return ProfessionCode::getIt(ProfessionCode::COMMONER);
        }

        return ProfessionCode::getIt($selectedProfession);
    }

    public function getCurrentOnHorseback(): bool
    {
        return (bool)$this->currentValues->getCurrentValue(FightRequest::ON_HORSEBACK);
    }

    public function getCurrentRidingSkillRank(): int
    {
        return (int)$this->currentValues->getCurrentValue(FightRequest::RIDING_SKILL_RANK);
    }

    public function getCurrentFightingFreeWillAnimal(): bool
    {
        return (bool)$this->currentValues->getCurrentValue(FightRequest::FIGHTING_FREE_WILL_ANIMAL);
    }

    public function getCurrentZoologySkillRank(): int
    {
        return (int)$this->currentValues->getCurrentValue(FightRequest::ZOOLOGY_SKILL_RANK);
    }
}