<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Body;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\Partials\AbstractIntegerProperty;

/**
 * @method static Age getIt(int | \Granam\Integer\IntegerInterface $value)
 * @method Age add(int | \Granam\Integer\IntegerInterface $value)
 * @method Age sub(int | \Granam\Integer\IntegerInterface $value)
 */
class Age extends AbstractIntegerProperty implements BodyProperty
{
    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::AGE);
    }
}