<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use Granam\Strict\Object\StrictObject;

class Redirect extends StrictObject
{
    private string $target;
    private int $afterSeconds;

    public function __construct(string $target, int $afterSeconds)
    {
        $this->target = $target;
        $this->afterSeconds = $afterSeconds;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getAfterSeconds(): int
    {
        return $this->afterSeconds;
    }
}
