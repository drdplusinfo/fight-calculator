<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Cache;

use Granam\Strict\Object\StrictObject;

class ContentIrrelevantRequestAlias extends StrictObject
{
    private string $truePath;
    private array $trueParameters;
    private string $aliasPath;
    private array $aliasGetParameters;

    public function __construct(string $truePath, array $trueParameters, string $aliasPath, array $aliasGetParameters)
    {
        $this->truePath = $truePath;
        $this->trueParameters = $trueParameters;
        $this->aliasPath = $aliasPath;
        $this->aliasGetParameters = $aliasGetParameters;
    }

    public function getTruePath(): string
    {
        return $this->truePath;
    }

    public function getTrueParameters(): array
    {
        return $this->trueParameters;
    }

    public function getAliasPath(): string
    {
        return $this->aliasPath;
    }

    public function getAliasGetParameters(): array
    {
        return $this->aliasGetParameters;
    }
}
