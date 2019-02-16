<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Partials;

use DrdPlus\BaseProperties\Property;
use Granam\ScalarEnum\ScalarEnum;
use Granam\String\StringInterface;

/**
 * @method static AbstractFloatProperty getEnum(string $enumValue)
 */
abstract class AbstractStringProperty extends ScalarEnum implements Property, StringInterface
{
    public function getValue(): string
    {
        return parent::getValue();
    }

}