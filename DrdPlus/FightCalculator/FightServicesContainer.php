<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\AttackSkeleton\AttackServicesContainer;
use DrdPlus\AttackSkeleton\PreviousArmaments;
use DrdPlus\FightCalculator\Web\BasicFightPropertiesBody;
use DrdPlus\FightCalculator\Web\MeleeWeaponSkillBody;
use DrdPlus\FightCalculator\Web\ShieldFightPropertiesBody;

class FightServicesContainer extends AttackServicesContainer
{
    /** @var MeleeWeaponSkillBody */
    private $meleeWeaponSkillBody;
    /** @var CurrentArmamentsWithSkills */
    private $currentArmamentsWithSkills;
    /** @var CurrentProperties */
    private $currentProperties;
    /** @var Fight */
    private $fight;
    /** @var PreviousArmamentsWithSkills */
    private $previousArmamentsWithSkills;
    /** @var HistoryWithSkills */
    private $historyWithSkills;
    /** @var BasicFightPropertiesBody */
    private $basicFightPropertiesBody;
    /** @var ShieldFightPropertiesBody */
    private $shieldWithMeleeWeaponBody;
    /** @var ShieldFightPropertiesBody */
    private $shieldWithRangedWeaponBody;
    /** @var PreviousArmaments */
    private $previousArmaments;
    /** @var PreviousProperties */
    private $previousProperties;

    public function getRulesMainBodyParameters(): array
    {
        return [
            'historyDeletionBody' => $this->getHistoryDeletionBody(),
            'bodyPropertiesBody' => $this->getBodyPropertiesBody(),
            'bodyArmorBody' => $this->getBodyArmorBody(),
            'helmBody' => $this->getHelmBody(),
            'meleeWeaponBody' => $this->getMeleeWeaponBody(),
            'meleeWeaponSkillBody' => $this->getMeleeWeaponSkillBody(),
            'rangedWeaponBody' => $this->getRangedWeaponBody(),
            'shieldBody' => $this->getShieldBody(),
            'withoutShield' => $this->getCurrentArmaments()->getCurrentShield()->isUnarmed(),
            'shieldWithMeleeWeaponBody' => $this->getShieldWithMeleeWeaponBody(),
            'shieldWithRangedWeaponBody' => $this->getShieldWithRangedWeaponBody(),
            'calculatorDebugContactsBody' => $this->getCalculatorDebugContactsBody(),
        ];
    }

    public function getShieldWithMeleeWeaponBody(): ShieldFightPropertiesBody
    {
        if ($this->shieldWithMeleeWeaponBody === null) {
            $this->shieldWithMeleeWeaponBody = new ShieldFightPropertiesBody(
                $this->getCurrentArmaments()->getCurrentMeleeShieldHolding(),
                $this->getPreviousArmaments()->getPreviousMeleeShieldHolding(),
                $this->getFight()->getCurrentMeleeShieldFightProperties(),
                $this->getFight()->getPreviousMeleeShieldFightProperties(),
                $this->getFight(),
                $this->getHtmlHelper()
            );
        }
        return $this->shieldWithMeleeWeaponBody;
    }

    public function getShieldWithRangedWeaponBody(): ShieldFightPropertiesBody
    {
        if ($this->shieldWithRangedWeaponBody === null) {
            $this->shieldWithRangedWeaponBody = new ShieldFightPropertiesBody(
                $this->getCurrentArmaments()->getCurrentRangedShieldHolding(),
                $this->getPreviousArmaments()->getPreviousRangedShieldHolding(),
                $this->getFight()->getCurrentRangedShieldFightProperties(),
                $this->getFight()->getPreviousRangedShieldFightProperties(),
                $this->getFight(),
                $this->getHtmlHelper()
            );
        }
        return $this->shieldWithRangedWeaponBody;
    }

    public function getPreviousArmaments(): PreviousArmaments
    {
        if ($this->previousArmaments === null) {
            $this->previousArmaments = new PreviousArmaments(
                $this->getHistory(),
                $this->getPreviousProperties(),
                $this->getArmourer(),
                $this->getTables()
            );
        }
        return $this->previousArmaments;
    }

    public function getPreviousProperties(): PreviousProperties
    {
        if ($this->previousProperties === null) {
            $this->previousProperties = new PreviousProperties($this->getHistory(), $this->getTables());
        }
        return $this->previousProperties;
    }

    public function getMeleeWeaponSkillBody(): MeleeWeaponSkillBody
    {
        if ($this->meleeWeaponSkillBody === null) {
            $this->meleeWeaponSkillBody = new MeleeWeaponSkillBody(
                $this->getCurrentArmamentsWithSkills(),
                $this->getFight(),
                $this->getHtmlHelper()
            );
        }
        return $this->meleeWeaponSkillBody;
    }

    public function getCurrentArmamentsWithSkills(): CurrentArmamentsWithSkills
    {
        if ($this->currentArmamentsWithSkills === null) {
            $this->currentArmamentsWithSkills = new CurrentArmamentsWithSkills(
                $this->getCurrentProperties(),
                $this->getCurrentArmamentsValues(),
                $this->getArmourer(),
                $this->getCustomArmamentsRegistrar(),
                $this->getCurrentValues()
            );
        }
        return $this->currentArmamentsWithSkills;
    }

    /**
     * @return \DrdPlus\AttackSkeleton\CurrentProperties|CurrentProperties
     */
    public function getCurrentProperties(): \DrdPlus\AttackSkeleton\CurrentProperties
    {
        if ($this->currentProperties === null) {
            $this->currentProperties = new CurrentProperties($this->getCurrentValues(), $this->getTables());
        }

        return $this->currentProperties;
    }

    public function getFight(): Fight
    {
        if ($this->fight === null) {
            $this->fight = new Fight(
                $this->getCurrentArmamentsWithSkills(),
                $this->getCurrentProperties(),
                $this->getCurrentValues(),
                $this->getPreviousArmamentsWithSkills(),
                $this->getPreviousProperties(),
                $this->getHistoryWithSkills(),
                $this->getArmourer(),
                $this->getTables()
            );
        }
        return $this->fight;
    }

    public function getPreviousArmamentsWithSkills(): PreviousArmamentsWithSkills
    {
        if ($this->previousArmamentsWithSkills === null) {
            $this->previousArmamentsWithSkills = new PreviousArmamentsWithSkills(
                $this->getHistory(),
                $this->getPreviousProperties(),
                $this->getArmourer(),
                $this->getTables()
            );
        }
        return $this->previousArmamentsWithSkills;
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
                $this->getHtmlHelper()
            );
        }
        return $this->basicFightPropertiesBody;
    }
}