<?php declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Derived\Speed;
use DrdPlus\Tables\Tables;

class CurrentProperties extends \DrdPlus\AttackSkeleton\CurrentProperties
{
    /**
     * @var Tables
     */
    private $tables;

    public function __construct(CurrentValues $currentValues, Tables $tables)
    {
        parent::__construct($currentValues);
        $this->tables = $tables;
    }

    public function getCurrentSpeed(): Speed
    {
        return Speed::getIt($this->getCurrentStrength(), $this->getCurrentAgility(), $this->getCurrentHeight());
    }

    public function getCurrentHeight(): Height
    {
        return $this->getCurrentHeightInCm()->getHeight($this->tables);
    }
}