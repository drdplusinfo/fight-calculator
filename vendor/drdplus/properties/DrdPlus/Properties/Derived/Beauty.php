<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\Properties\Derived\Partials\AspectOfVisage;
use DrdPlus\BaseProperties\Knack;

/**
 * @method Beauty add(int | \Granam\Integer\IntegerInterface $value)
 * @method Beauty sub(int | \Granam\Integer\IntegerInterface $value)
 */
class Beauty extends AspectOfVisage
{
    public static function getIt(Agility $agility, Knack $knack, Charisma $charisma): Beauty
    {
        return new static($agility, $knack, $charisma);
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::BEAUTY);
    }
}