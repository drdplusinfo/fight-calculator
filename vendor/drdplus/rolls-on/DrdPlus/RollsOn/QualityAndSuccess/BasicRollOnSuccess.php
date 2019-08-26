<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\QualityAndSuccess;

use Granam\Boolean\BooleanInterface;

class BasicRollOnSuccess extends SimpleRollOnSuccess implements BooleanInterface
{
    public function getValue(): bool
    {
        return $this->isSuccess();
    }
}