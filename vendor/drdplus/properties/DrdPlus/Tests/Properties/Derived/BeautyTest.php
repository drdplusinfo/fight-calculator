<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Derived;

use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Properties\Derived\Beauty;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;

class BeautyTest extends AbstractDerivedPropertyTest
{
    protected function createIt(int $value): AbstractDerivedProperty
    {
        return Beauty::getIt(Agility::getIt($value * 2), Knack::getIt(0), Charisma::getIt(0));
    }
}