<?php declare(strict_types=1);

namespace Granam\Boolean;

use Granam\Scalar\ScalarInterface;

interface BooleanInterface extends ScalarInterface
{
    public function getValue(): bool;
}
