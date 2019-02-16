<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Tables\Properties\SpeedInterface;

class Speed extends AbstractDerivedProperty implements SpeedInterface
{
    /**
     * @param Strength $strength
     * @param Agility $agility
     * @param Height $height
     * @return Speed
     */
    public static function getIt(Strength $strength, Agility $agility, Height $height): Speed
    {
        return new static(
            SumAndRound::average($strength->getValue(), $agility->getValue())
            + SumAndRound::ceil($height->getValue() / 3) - 2 // 1 -> 3 = -1; 4 -> 6 = 0; 7 -> 9 = +1 ...
        );
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::SPEED);
    }
}