<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\Web\AttackWebPartsContainer;
use DrdPlus\CalculatorSkeleton\CalculatorConfiguration;
use DrdPlus\CalculatorSkeleton\CalculatorServicesContainer;
use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\Web\Tools\WebFiles;
use DrdPlus\RulesSkeleton\Web\Tools\WebPartsContainer;
use DrdPlus\Tables\Tables;

/**
 * @method HtmlHelper getHtmlHelper
 */
class AttackServicesContainer extends CalculatorServicesContainer
{
    private ?\DrdPlus\AttackSkeleton\CurrentProperties $currentProperties = null;
    private ?\DrdPlus\AttackSkeleton\PossibleArmaments $possibleArmaments = null;
    private ?\DrdPlus\Armourer\Armourer $armourer = null;
    private ?\DrdPlus\Tables\Tables $tables = null;
    private ?\DrdPlus\AttackSkeleton\CurrentArmaments $currentArmaments = null;
    private ?\DrdPlus\AttackSkeleton\CustomArmamentsRegistrar $customArmamentsRegistrar = null;
    private ?\DrdPlus\AttackSkeleton\CustomArmamentAdder $customArmamentAdder = null;
    private ?\DrdPlus\AttackSkeleton\CurrentArmamentsValues $currentArmamentsValues = null;
    private ?\DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages $armamentsUsabilityMessages = null;
    private ?\DrdPlus\AttackSkeleton\AttackRequest $attackRequest = null;
    private ?\DrdPlus\AttackSkeleton\CustomArmamentsState $customArmamentsState = null;
    private ?\DrdPlus\AttackSkeleton\Web\AttackWebPartsContainer $routedAttackWebPartsContainer = null;
    private ?\DrdPlus\AttackSkeleton\Web\AttackWebPartsContainer $rootAttackWebPartsContainer = null;

    public function __construct(CalculatorConfiguration $calculatorConfiguration, Environment $environment, HtmlHelper $htmlHelper)
    {
        parent::__construct($calculatorConfiguration, $environment, $htmlHelper);
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

    /**
     * @return Request|AttackRequest
     */
    public function getRequest(): Request
    {
        if ($this->attackRequest === null) {
            $this->attackRequest = AttackRequest::createFromGlobals($this->getBotParser(), $this->getEnvironment());
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

    public function getRoutedWebPartsContainer(): WebPartsContainer
    {
        if ($this->routedAttackWebPartsContainer === null) {
            $this->routedAttackWebPartsContainer = $this->createAttackWebPartsContainer($this->getRoutedWebFiles());
        }
        return $this->routedAttackWebPartsContainer;
    }

    private function createAttackWebPartsContainer(WebFiles $webFiles): AttackWebPartsContainer
    {
        return new AttackWebPartsContainer(
            $this->getConfiguration(),
            $this->getUsagePolicy(),
            $webFiles,
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

    public function getRootWebPartsContainer(): WebPartsContainer
    {
        if ($this->rootAttackWebPartsContainer === null) {
            $this->rootAttackWebPartsContainer = $this->createAttackWebPartsContainer($this->getRootWebFiles());
        }
        return $this->rootAttackWebPartsContainer;
    }

}
