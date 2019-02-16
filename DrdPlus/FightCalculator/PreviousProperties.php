<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\CalculatorSkeleton\History;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Derived\Speed;
use DrdPlus\Tables\Tables;

class PreviousProperties extends \DrdPlus\AttackSkeleton\PreviousProperties
{
    /**
     * @var Tables
     */
    private $tables;

    public function __construct(History $history, Tables $tables)
    {
        parent::__construct($history);
        $this->tables = $tables;
    }

    public function getPreviousSpeed(): Speed
    {
        return Speed::getIt($this->getPreviousStrength(), $this->getPreviousAgility(), $this->getPreviousHeight());
    }

    public function getPreviousHeight(): Height
    {
        return $this->getPreviousHeightInCm()->getHeight($this->tables);
    }
}