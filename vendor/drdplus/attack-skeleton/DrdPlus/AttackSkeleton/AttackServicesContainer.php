<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\Web\AttackWebPartsContainer;
use DrdPlus\CalculatorSkeleton\CalculatorConfiguration;
use DrdPlus\CalculatorSkeleton\CalculatorServicesContainer;
use DrdPlus\Tables\Tables;

/**
 * @method HtmlHelper getHtmlHelper
 */
class AttackServicesContainer extends CalculatorServicesContainer
{
    /** @var CurrentProperties */
    private $currentProperties;
    /** @var PossibleArmaments */
    private $possibleArmaments;
    /** @var Armourer */
    private $armourer;
    /** @var Tables */
    private $tables;
    /** @var CurrentArmaments */
    private $currentArmaments;
    /** @var CustomArmamentsRegistrar */
    private $customArmamentsRegistrar;
    /** @var CustomArmamentAdder */
    private $customArmamentAdder;
    /** @var CurrentArmamentsValues */
    private $currentArmamentsValues;
    /** @var ArmamentsUsabilityMessages */
    private $armamentsUsabilityMessages;
    /** @var AttackRequest */
    private $attackRequest;
    /** @var CustomArmamentsState */
    private $customArmamentsState;
    /** @var AttackWebPartsContainer */
    private $attackWebPartsContainer;

    public function __construct(CalculatorConfiguration $calculatorConfiguration, HtmlHelper $htmlHelper)
    {
        parent::__construct($calculatorConfiguration, $htmlHelper);
    }

    public function getCurrentProperties(): CurrentProperties
    {
        if ($this->currentProperties === null) {
            $this->currentProperties = new CurrentProperties($this->getCurrentValues());
        }

        return $this->currentProperties;
    }

    public function getPossibleArmaments(): PossibleArmaments
    {
        if ($this->possibleArmaments === null) {
            $this->possibleArmaments = new PossibleArmaments(
                $this->getArmourer(),
                $this->getCurrentProperties(),
                $this->getCurrentArmaments()->getCurrentMeleeWeaponHolding(),
                $this->getCurrentArmaments()->getCurrentRangedWeaponHolding()
            );
        }

        return $this->possibleArmaments;
    }

    public function getArmourer(): Armourer
    {
        if ($this->armourer === null) {
            $this->armourer = new Armourer($this->getTables());
        }
        return $this->armourer;
    }

    public function getCurrentArmaments(): CurrentArmaments
    {
        if ($this->currentArmaments === null) {
            $this->currentArmaments = new CurrentArmaments(
                $this->getCurrentProperties(),
                $this->getCurrentArmamentsValues(),
                $this->getArmourer(),
                $this->getCustomArmamentsRegistrar()
            );
        }

        return $this->currentArmaments;
    }

    public function getCustomArmamentsRegistrar(): CustomArmamentsRegistrar
    {
        if ($this->customArmamentsRegistrar === null) {
            $this->customArmamentsRegistrar = new CustomArmamentsRegistrar(
                $this->getCustomArmamentAdder(),
                $this->getTables()
            );
        }
        return $this->customArmamentsRegistrar;
    }

    public function getTables(): Tables
    {
        if ($this->tables === null) {
            $this->tables = Tables::getIt();
        }
        return $this->tables;
    }

    public function getAttackRequest(): AttackRequest
    {
        if ($this->attackRequest === null) {
            $this->attackRequest = new AttackRequest($this->getCurrentValues(), $this->getBotParser());
        }
        return $this->attackRequest;
    }

    public function getCustomArmamentAdder(): CustomArmamentAdder
    {
        if ($this->customArmamentAdder === null) {
            $this->customArmamentAdder = new CustomArmamentAdder($this->getArmourer());
        }
        return $this->customArmamentAdder;
    }

    public function getCurrentArmamentsValues(): CurrentArmamentsValues
    {
        if ($this->currentArmamentsValues === null) {
            $this->currentArmamentsValues = new CurrentArmamentsValues($this->getCurrentValues());
        }
        return $this->currentArmamentsValues;
    }

    public function getArmamentsUsabilityMessages(): ArmamentsUsabilityMessages
    {
        if ($this->armamentsUsabilityMessages === null) {
            $this->armamentsUsabilityMessages = new ArmamentsUsabilityMessages($this->getPossibleArmaments());
        }
        return $this->armamentsUsabilityMessages;
    }

    public function getCustomArmamentsState(): CustomArmamentsState
    {
        if ($this->customArmamentsState === null) {
            $this->customArmamentsState = new CustomArmamentsState($this->getCurrentValues());
        }
        return $this->customArmamentsState;
    }

    public function getWebPartsContainer(): \DrdPlus\RulesSkeleton\Web\WebPartsContainer
    {
        if ($this->attackWebPartsContainer === null) {
            $this->attackWebPartsContainer = new AttackWebPartsContainer(
                $this->getPass(),
                $this->getWebFiles(),
                $this->getDirs(),
                $this->getHtmlHelper(),
                $this->getRequest(),
                $this->getCurrentProperties(),
                $this->getCustomArmamentsState(),
                $this->getCurrentArmamentsValues(),
                $this->getCurrentArmaments(),
                $this->getPossibleArmaments(),
                $this->getArmamentsUsabilityMessages(),
                $this->getArmourer()
            );
        }
        return $this->attackWebPartsContainer;
    }

}