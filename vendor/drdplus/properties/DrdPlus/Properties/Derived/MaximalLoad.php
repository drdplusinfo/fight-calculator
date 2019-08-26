<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Tables\Properties\AthleticsInterface;

/**
 * See PPH page 114 left column, @link https://pph.drdplus.info/#nalozeni
 * @method MaximalLoad add(int | \Granam\Integer\IntegerInterface $value)
 * @method MaximalLoad sub(int | \Granam\Integer\IntegerInterface $value)
 */
class MaximalLoad extends AbstractDerivedProperty
{
    public static function getIt(Strength $strength, AthleticsInterface $athletics): MaximalLoad
    {
        return new static($strength->getValue() + 21 + $athletics->getAthleticsBonus()->getValue());
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::MAXIMAL_LOAD);
    }

}