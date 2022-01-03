<?php declare(strict_types=1);

namespace Granam\WebVersions;

use Granam\Git\Git;
use Granam\Strict\Object\StrictObject;

/**
 * Reader of Git branches and tags defining available versions of web files
 */
class WebVersions extends StrictObject
{

    public const LAST_UNSTABLE_VERSION = 'main';

    /** @var Git */
    private $git;
    /** @var string */
    private $repositoryDir;
    /** @var string */
    private $lastUnstableVersion;

    public function __construct(Git $git, string $repositoryDir, string $lastUnstableVersion = self::LAST_UNSTABLE_VERSION)
    {
        $this->git = $git;
        $this->repositoryDir = $repositoryDir;
        $this->lastUnstableVersion = $lastUnstableVersion;
    }

    /**
     * Intentionally are versions taken from branches only, not tags, to lower amount of versions to switch into.
     * @return array|string[] Includes last unstable version, probably "main"
     */
    public function getAllMinorVersions(): array
    {
        $minorVersions = $this->getAllStableMinorVersions();
        array_unshift($minorVersions, $this->getLastUnstableVersion());
        return $minorVersions;
    }

    /**
     * Intentionally are minor versions taken from branches only, not tags.
     * @return array|string[]
     */
    public function getAllStableMinorVersions(): array
    {
        return $this->git->getAllMinorVersions($this->repositoryDir);
    }

    /**
     * @return array|string[]
     */
    public function getAllPatchVersions(): array
    {
        $patchVersions = $this->getAllStablePatchVersions();
        array_unshift($patchVersions, $this->getLastUnstableVersion());
        return $patchVersions;
    }

    /**
     * Intentionally are patch versions taken from tags only, not branches.
     * @return array|string[]
     */
    public function getAllStablePatchVersions(): array
    {
        return $this->git->getAllPatchVersions($this->repositoryDir);
    }

    /**
     * @return string|null Last stable minor version, if any, or null
     */
    public function getLastStableMinorVersion(): ?string
    {
        return $this->git->getLastStableMinorVersion($this->repositoryDir);
    }

    /**
     * @return string|null Last stable patch version, if any
     */
    public function getLastStablePatchVersion(): ?string
    {
        return $this->git->getLastPatchVersion($this->repositoryDir);
    }

    public function getLastUnstableVersion(): string
    {
        return $this->lastUnstableVersion;
    }

    public function hasMinorVersion(string $minorVersion): bool
    {
        return in_array($minorVersion, $this->getAllMinorVersions(), true);
    }

    public function getLastPatchVersionOf(string $superiorVersion): string
    {
        if ($superiorVersion === $this->getLastUnstableVersion()) {
            return $superiorVersion;
        }
        if (strpos($superiorVersion, '(HEAD detached at ') === 0) {
            return $superiorVersion;
        }
        return $this->git->getLastPatchVersionOf($superiorVersion, $this->repositoryDir);
    }
}
