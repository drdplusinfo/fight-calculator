<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use Granam\Strict\Object\StrictObject;

class Environment extends StrictObject
{
    private string $phpSapi;
    private ?string $projectEnvironment = null;
    private ?string $remoteAddress = null;
    private ?string $forcedMode = null;

    public static function createFromGlobals(): Environment
    {
        return new static(
            \PHP_SAPI,
            $_ENV['PROJECT_ENVIRONMENT'] ?? null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_GET['mode'] ?? null
        );
    }

    public function __construct(
        string $phpSapi,
        string $projectEnvironment = null,
        string $remoteAddress = null,
        string $forcedMode = null
    )
    {
        $this->phpSapi = $phpSapi;
        $this->projectEnvironment = $projectEnvironment;
        $this->remoteAddress = $remoteAddress;
        $this->forcedMode = $forcedMode ? trim($forcedMode) : null;
    }

    public function isCliRequest(): bool
    {
        return in_array($this->getPhpSapi(), ['cli', 'phpdbg', 'embed'], true);
    }

    public function getPhpSapi(): string
    {
        return $this->phpSapi;
    }

    public function isOnDevEnvironment(): bool
    {
        return $this->projectEnvironment && stripos($this->projectEnvironment, 'dev') === 0;
    }

    public function isOnLocalhost(): bool
    {
        return $this->remoteAddress === '127.0.0.1';
    }

    public function isInProduction(): bool
    {
        return $this->isOnForcedProductionMode()
            || (!$this->isOnDevEnvironment() && (!$this->isCliRequest() && !$this->isOnLocalhost()));
    }

    public function isOnForcedProductionMode(): bool
    {
        return $this->forcedMode && stripos($this->forcedMode, 'prod') === 0;
    }

    public function isOnForcedDevelopmentMode(): bool
    {
        return $this->forcedMode && stripos($this->forcedMode, 'dev') === 0;
    }
}
