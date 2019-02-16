<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Derived\Dangerousness;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;

class DangerousnessTest extends AbstractDerivedPropertyTest
{
    protected function createIt(int $value): AbstractDerivedProperty
    {
        return Dangerousness::getIt(Strength::getIt($value * 2), Will::getIt(0), Charisma::getIt(0));
    }
}