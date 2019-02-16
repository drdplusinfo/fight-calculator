<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Derived\Dignity;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;

class DignityTest extends AbstractDerivedPropertyTest
{
    protected function createIt(int $value): AbstractDerivedProperty
    {
        return Dignity::getIt(Intelligence::getIt($value * 2), Will::getIt(0), Charisma::getIt(0));
    }
}