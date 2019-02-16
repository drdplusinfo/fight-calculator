<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\Properties\Derived\Partials\AspectOfVisage;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Will;

/**
 * @method Dignity add(int | \Granam\Integer\IntegerInterface $value)
 * @method Dignity sub(int | \Granam\Integer\IntegerInterface $value)
 */
class Dignity extends AspectOfVisage
{
    public static function getIt(Intelligence $intelligence, Will $will, Charisma $charisma): Dignity
    {
        return new static($intelligence, $will, $charisma);
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::DIGNITY);
    }
}