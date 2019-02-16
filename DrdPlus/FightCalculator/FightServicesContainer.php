<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\AttackSkeleton\AttackServicesContainer;
use DrdPlus\FightCalculator\Web\BasicFightPropertiesBody;
use DrdPlus\FightCalculator\Web\MeleeWeaponSkillBody;
use DrdPlus\Tables\Tables;

class FightServicesContainer extends AttackServicesContainer
{
    /** @var MeleeWeaponSkillBody */
    private $meleeWeaponSkillBody;
    /** @var Fight */
    private $fight;
    /** @var HistoryWithSkills */
    private $historyWithSkills;
    /** @var BasicFightPropertiesBody */
    private $basicFightPropertiesBody;

    public function getRulesMainBodyParameters(): array
    {
        return [
            'historyDeletion' => $this->getHistoryDeletionBody(),
            'bodyProperties' => $this->getBodyPropertiesBody(),
            'bodyArmor' => $this->getBodyArmorBody(),
            'helm' => $this->getHelmBody(),
            'meleeWeapon' => $this->getMeleeWeaponBody(),
            'meleeWeaponSkill' => $this->getMeleeWeaponSkillBody(),
            'rangedWeapon' => $this->getRangedWeaponBody(),
            'shield' => $this->getShieldBody(),
            'calculatorDebugContacts' => $this->getCalculatorDebugContactsBody(),
        ];
    }

    public function getMeleeWeaponSkillBody(): MeleeWeaponSkillBody
    {
        if ($this->meleeWeaponSkillBody === null) {
            $this->meleeWeaponSkillBody = new MeleeWeaponSkillBody();
        }
        return $this->meleeWeaponSkillBody;
    }

    public function getFight(): Fight
    {
        if ($this->fight === null) {
            $this->fight = new Fight(
                $this->getCurrentValues(),
                $this->getCurrentProperties(),
                $this->getHistoryWithSkillRanks(),
                new PreviousProperties($this->getHistoryWithSkillRanks()),
                new CustomArmamentsService(),
                Tables::getIt()
            );
        }
        return $this->fight;
    }

    public function getHistoryWithSkills(): HistoryWithSkills
    {
        if ($this->historyWithSkills === null) {
            $this->historyWithSkills = new HistoryWithSkills(
                [
                    FightRequest::MELEE_FIGHT_SKILL => FightRequest::MELEE_FIGHT_SKILL_RANK,
                    FightRequest::RANGED_FIGHT_SKILL => FightRequest::RANGED_FIGHT_SKILL_RANK,
                    FightRequest::RANGED_FIGHT_SKILL => FightRequest::RANGED_FIGHT_SKILL_RANK,
                ],
                $this->getCookiesService()

            );
        }
        return $this->historyWithSkills;
    }

    public function getBasicFightPropertiesBody(): BasicFightPropertiesBody
    {
        if ($this->basicFightPropertiesBody === null) {
            $this->basicFightPropertiesBody = new BasicFightPropertiesBody(
                $this->getFight(),
                $this->getCurrentArmaments(),
                $this->getPreviousArmaments(),
                $this->getHtmlHelper(),
                $this->getHtmlHelper()
            );
        }
        return $this->basicFightPropertiesBody;
    }
}