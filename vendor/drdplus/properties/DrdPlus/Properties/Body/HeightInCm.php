<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Body;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\Partials\AbstractFloatProperty;
use DrdPlus\Tables\Tables;
use Granam\Number\NumberInterface;

/**
 * @method static HeightInCm getIt(float | NumberInterface $value)
 */
class HeightInCm extends AbstractFloatProperty implements BodyProperty
{
    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::HEIGHT_IN_CM);
    }

    public function getHeight(Tables $tables): Height
    {
        return Height::getIt($this, $tables);
    }

}