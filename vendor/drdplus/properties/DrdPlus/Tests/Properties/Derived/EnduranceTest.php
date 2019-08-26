<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Derived\Endurance;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;

class EnduranceTest extends AbstractDerivedPropertyTest
{
    protected function createIt(int $value): AbstractDerivedProperty
    {
        return Endurance::getIt(Strength::getIt($value * 2), Will::getIt(0));
    }
}