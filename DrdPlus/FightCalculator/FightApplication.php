<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\CalculatorSkeleton\CalculatorApplication;

class FightApplication extends CalculatorApplication
{
    /**
     * @var FightServicesContainer
     */
    private $fightServicesContainer;

    public function __construct(FightServicesContainer $fightServicesContainer)
    {
        parent::__construct($fightServicesContainer);
        $this->fightServicesContainer = $fightServicesContainer;
    }

    public function run(): void
    {
        $this->solveSkillsHistory();
        parent::run();
    }

    private function solveSkillsHistory(): void
    {
        $request = $this->fightServicesContainer->getRequest();
        $skillsHistory = $this->fightServicesContainer->getSkillsHistory();
        if ($request->isRequestedHistoryDeletion()) {
            $skillsHistory->deleteSkillsHistory();
        }
        $skillsHistory->saveSkillsHistory($request->getValuesFromGet());
    }

}