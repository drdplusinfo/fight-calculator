<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton;

use Granam\Strict\Object\StrictObject;

class DateTimeProvider extends StrictObject
{
    private \DateTimeInterface $now;

    public function __construct(\DateTimeInterface $now)
    {
        $this->now = $now;
    }

    public function getNow(): \DateTimeInterface
    {
        return $this->now;
    }
}
