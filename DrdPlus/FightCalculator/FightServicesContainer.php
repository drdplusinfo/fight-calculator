<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\AttackSkeleton\AttackServicesContainer;
use DrdPlus\AttackSkeleton\PreviousArmaments;
use DrdPlus\FightCalculator\Web\FightWebPartsContainer;

class FightServicesContainer extends AttackServicesContainer
{

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
    /** @var PreviousArmaments */
    private $previousArmaments;
    /** @var PreviousProperties */
    private $previousProperties;
    /** @var FightWebPartsContainer */
    private $fightWebPartsContainer;

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

    public function getWebPartsContainer(): \DrdPlus\RulesSkeleton\Web\WebPartsContainer
    {
        if ($this->fightWebPartsContainer === null) {
            $this->fightWebPartsContainer = new FightWebPartsContainer($this);
        }
        return $this->fightWebPartsContainer;
    }

}