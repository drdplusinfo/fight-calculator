<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\AttackSkeleton\AttackServicesContainer;
use DrdPlus\AttackSkeleton\PreviousArmaments;
use DrdPlus\FightCalculator\Web\BasicFightPropertiesBody;
use DrdPlus\FightCalculator\Web\FightPropertiesBody;
use DrdPlus\FightCalculator\Web\MeleeWeaponSkillBody;
use DrdPlus\FightCalculator\Web\ProfessionsBody;
use DrdPlus\FightCalculator\Web\RangedTargetBody;
use DrdPlus\FightCalculator\Web\RangedWeaponSkillBody;
use DrdPlus\FightCalculator\Web\ShieldFightPropertiesBody;

class FightServicesContainer extends AttackServicesContainer
{
    /** @var MeleeWeaponSkillBody */
    private $meleeWeaponSkillBody;
    /** @var RangedWeaponSkillBody */
    private $rangedWeaponSkillBody;
    /** @var FightPropertiesBody */
    private $rangedWeaponFightPropertiesBody;
    /** @var RangedTargetBody */
    private $rangedTargetBody;
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
    /** @var ProfessionsBody */
    private $professionsBody;
    /** @var FightPropertiesBody */
    private $meleeWeaponFightPropertiesBody;

    public function getRulesMainBodyParameters(): array
    {
        return [
            'basicFightPropertiesBody' => $this->getBasicFightPropertiesBody(),
            // armors
            'bodyArmorBody' => $this->getBodyArmorBody(),
            'helmBody' => $this->getHelmBody(),
            // melee
            'meleeWeaponBody' => $this->getMeleeWeaponBody(),
            'meleeWeaponSkillBody' => $this->getMeleeWeaponSkillBody(),
            'meleeWeaponFightPropertiesBody' => $this->getMeleeWeaponFightPropertiesBody(),
            // ranged
            'rangedWeaponBody' => $this->getRangedWeaponBody(),
            'rangedWeaponSkillBody' => $this->getRangedWeaponSkillBody(),
            'rangedWeaponFightPropertiesBody' => $this->getRangedWeaponFightPropertiesBody(),
            'rangedTargetBody' => $this->getRangedTargetBody(),
            // shield
            'shieldBody' => $this->getShieldBody(),
            'shieldWithMeleeWeaponBody' => $this->getShieldWithMeleeWeaponBody(),
            'shieldWithRangedWeaponBody' => $this->getShieldWithRangedWeaponBody(),
            'withoutShield' => $this->getCurrentArmaments()->getCurrentShield()->isUnarmed(),
            // others
            'professionsBody' => $this->getProfessionsBody(),
            'historyDeletionBody' => $this->getHistoryDeletionBody(),
            'bodyPropertiesBody' => $this->getBodyPropertiesBody(),
            'calculatorDebugContactsBody' => $this->getCalculatorDebugContactsBody(),
        ];
    }

    public function getMeleeWeaponFightPropertiesBody(): FightPropertiesBody
    {
        if ($this->meleeWeaponFightPropertiesBody === null) {
            $this->meleeWeaponFightPropertiesBody = new FightPropertiesBody(
                $this->getFight()->getCurrentMeleeWeaponFightProperties(),
                $this->getFight()->getPreviousMeleeWeaponFightProperties(),
                $this->getFight(),
                $this->getHtmlHelper()
            );
        }
        return $this->meleeWeaponFightPropertiesBody;
    }

    public function getProfessionsBody(): ProfessionsBody
    {
        if ($this->professionsBody === null) {
            $this->professionsBody = new ProfessionsBody(
                $this->getCurrentArmamentsWithSkills(),
                $this->getHtmlHelper()
            );
        }
        return $this->professionsBody;
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

    public function getRangedWeaponSkillBody(): RangedWeaponSkillBody
    {
        if ($this->rangedWeaponSkillBody === null) {
            $this->rangedWeaponSkillBody = new RangedWeaponSkillBody(
                $this->getCurrentArmamentsWithSkills(),
                $this->getFight(),
                $this->getHtmlHelper()
            );
        }
        return $this->rangedWeaponSkillBody;
    }

    public function getRangedWeaponFightPropertiesBody(): FightPropertiesBody
    {
        if ($this->rangedWeaponFightPropertiesBody === null) {
            $this->rangedWeaponFightPropertiesBody = new FightPropertiesBody(
                $this->getFight()->getCurrentRangedWeaponFightProperties(),
                $this->getFight()->getPreviousRangedWeaponFightProperties(),
                $this->getFight(),
                $this->getHtmlHelper()
            );
        }
        return $this->rangedWeaponFightPropertiesBody;
    }

    public function getRangedTargetBody(): RangedTargetBody
    {
        if ($this->rangedTargetBody === null) {
            $this->rangedTargetBody = new RangedTargetBody(
                $this->getFight(),
                $this->getRangedWeaponFightPropertiesBody(),
                $this->getHtmlHelper(),
                $this->getTables()
            );
        }
        return $this->rangedTargetBody;
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
                $this->getCookiesService(),
                $this->getRequest(),
                $this->getConfiguration()
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