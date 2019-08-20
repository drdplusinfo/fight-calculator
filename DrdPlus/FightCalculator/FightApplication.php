<?php declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\CalculatorSkeleton\CalculatorApplication;

class FightApplication extends CalculatorApplication
{
    public function __construct(FightServicesContainer $fightServicesContainer)
    {
        parent::__construct($fightServicesContainer);
    }
}