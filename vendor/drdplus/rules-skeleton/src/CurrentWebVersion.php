<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\Configurations\Dirs;
use Granam\Git\Git;
use Granam\Strict\Object\StrictObject;
use Granam\WebVersions\WebVersions;

/**
 * Reader of GIT tags defining available versions of web filesF
 */
class CurrentWebVersion extends StrictObject
{

    private Dirs $dirs;
    private ?string $currentCommitHash = null;
    private ?string $currentPatchVersion = null;
    private Git $git;
    private WebVersions $webVersions;

    public function __construct(Dirs $dirs, Git $git, WebVersions $webVersions)
    {
        $this->dirs = $dirs;
        $this->git = $git;
        $this->webVersions = $webVersions;
    }

    public function getCurrentMinorVersion(): string
    {
        return $this->git->getCurrentBranchName($this->dirs->getProjectRoot());
    }

    public function getCurrentPatchVersion(): string
    {
        if ($this->currentPatchVersion === null) {
            if ($this->webVersions->getLastUnstableVersion() === $this->getCurrentMinorVersion()) {
                $this->currentPatchVersion = $this->getCurrentMinorVersion(); // master, main...
            } else {
                $this->currentPatchVersion = $this->webVersions->getLastPatchVersionOf($this->getCurrentMinorVersion());
            }
        }

        return $this->currentPatchVersion;
    }

    public function getCurrentCommitHash(): string
    {
        if ($this->currentCommitHash === null) {
            $this->currentCommitHash = $this->git->getLastCommitHash($this->dirs->getProjectRoot());
        }

        return $this->currentCommitHash;
    }
}
