<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Derived\Partials;

use DrdPlus\BaseProperties\Charisma;
use DrdPlus\Calculations\SumAndRound;
use Granam\Integer\IntegerInterface;

abstract class AspectOfVisage extends AbstractDerivedProperty
{
    /**
     * @param IntegerInterface $firstProperty
     * @param IntegerInterface $secondProperty
     * @param Charisma $charisma
     */
    protected function __construct(IntegerInterface $firstProperty, IntegerInterface $secondProperty, Charisma $charisma)
    {
        parent::__construct(
            SumAndRound::round(($firstProperty->getValue() + $secondProperty->getValue()) / 2 + $charisma->getValue() / 2)
        );
    }
}