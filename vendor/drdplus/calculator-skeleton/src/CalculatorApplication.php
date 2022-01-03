<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton;

use DrdPlus\RulesSkeleton\RulesApplication;

class CalculatorApplication extends RulesApplication
{
    private CalculatorServicesContainer $calculatorServicesContainer;

    public function __construct(CalculatorServicesContainer $calculationServicesContainer)
    {
        parent::__construct($calculationServicesContainer);
        $this->calculatorServicesContainer = $calculationServicesContainer;
    }

    public function run(): void
    {
        $this->solveMemory();
        $this->solveHistory();
        parent::run();
    }

    private function solveMemory(): void
    {
        $request = $this->calculatorServicesContainer->getRequest();
        $memory = $this->calculatorServicesContainer->getMemory();
        if ($request->isRequestedHistoryDeletion()) {
            $memory->deleteMemory();
        }
        $memory->saveMemory($request->getValuesFromGet());
    }

    private function solveHistory(): void
    {
        $request = $this->calculatorServicesContainer->getRequest();
        $history = $this->calculatorServicesContainer->getHistory();
        if ($request->isRequestedHistoryDeletion()) {
            $history->deleteHistory();
        }
        $history->saveHistory($request->getValuesFromGet());
    }

    protected function createContent(): CalculatorContent
    {
        $rulesHtmlDocumentPostProcessor = $this->createRulesHtmlDocumentPostProcessor(
            $this->calculatorServicesContainer->getPassedMenuBody(),
            $this->calculatorServicesContainer->getPassedWebCache()
        );
        return new CalculatorContent(
            $this->calculatorServicesContainer->getRulesMainContent(),
            $this->calculatorServicesContainer->getPassedWebCache(),
            $rulesHtmlDocumentPostProcessor
        );
    }
}
