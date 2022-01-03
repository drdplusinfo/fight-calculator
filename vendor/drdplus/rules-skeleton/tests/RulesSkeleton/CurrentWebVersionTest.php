<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Granam\WebVersions\WebVersions;
use Granam\Git\Git;
use PHPUnit\Framework\TestCase;

class CurrentWebVersionTest extends AbstractContentTest
{

    /**
     * @test
     */
    public function I_can_get_current_version(): void
    {
        $git = $this->createGitWithCurrentBranchName('foo');
        self::assertSame(
            'foo',
            $this->createCurrentWebVersion(null, $git)->getCurrentMinorVersion()
        );
    }

    private function createGitWithCurrentBranchName(string $branchName): Git
    {
        return new class($branchName, $this->getProjectRoot()) extends Git
        {
            private string $branchName;
            private string $expectedRepositoryDir;

            public function __construct(string $branchName, string $expectedRepositoryDir)
            {
                parent::__construct();
                $this->branchName = $branchName;
                $this->expectedRepositoryDir = $expectedRepositoryDir;
            }

            public function getCurrentBranchName(string $repositoryDir): string
            {
                TestCase::assertSame($this->expectedRepositoryDir, $repositoryDir);

                return $this->branchName;
            }

        };
    }

    /**
     * @test
     */
    public function I_can_get_current_patch_version(): void
    {
        $webVersions = $this->createWebVersions();
        $currentWebVersion = $this->createCurrentWebVersion();
        if ($currentWebVersion->getCurrentMinorVersion() === $webVersions->getLastUnstableVersion()) {
            self::assertSame(
                $webVersions->getLastUnstableVersion(),
                $currentWebVersion->getCurrentPatchVersion()
            );
        } else {
            self::assertMatchesRegularExpression(
                '~^' . preg_quote($currentWebVersion->getCurrentMinorVersion(), '~') . '[.]\d+$~',
                $currentWebVersion->getCurrentPatchVersion()
            );
        }
    }

    /**
     * @test
     */
    public function I_will_get_null_if_there_is_no_last_stable_version(): void
    {
        $gitWithoutLastStableVersion = $this->createGitWithoutLastStableVersion($this->getProjectRoot());
        $webVersions = $this->createWebVersions($gitWithoutLastStableVersion, $this->getProjectRoot());
        self::assertSame('master', $webVersions->getLastUnstableVersion());
        self::assertNull($webVersions->getLastStableMinorVersion());
    }

    private function createGitWithoutLastStableVersion(string $expectedRepositoryDir): Git
    {
        return new class($expectedRepositoryDir) extends Git
        {
            private string $expectedRepositoryDir;

            public function __construct(string $expectedRepositoryDir)
            {
                parent::__construct();
                $this->expectedRepositoryDir = $expectedRepositoryDir;
            }

            public function getLastStableMinorVersion(
                string $repositoryDir,
                bool $readLocal = self::INCLUDE_LOCAL_BRANCHES,
                bool $readRemote = self::INCLUDE_REMOTE_BRANCHES
            ): ?string
            {
                TestCase::assertSame($this->expectedRepositoryDir, $repositoryDir);

                return null;
            }

        };
    }

    /**
     * @test
     */
    public function I_can_get_current_commit_hash(): void
    {
        $currentWebVersion = $this->createCurrentWebVersion();
        $currentCommitHash = $currentWebVersion->getCurrentCommitHash(); // called before reading .git/HEAD to ensure it exists
        $lastCommitHashFromGitHeadFile = $this->getLastCommitHashFromGitHeadFile($this->getProjectRoot());
        self::assertSame(
            $lastCommitHashFromGitHeadFile,
            $currentCommitHash,
            \sprintf(
                'Expected different last commit for version %s taken from dir %s',
                $currentWebVersion->getCurrentMinorVersion(),
                $this->getProjectRoot()
            )
        );
    }

    /**
     * @param string $dir
     * @return string
     * @throws \Tests\DrdPlus\RulesSkeleton\Exceptions\CanNotReadGitHead
     */
    private function getLastCommitHashFromGitHeadFile(string $dir): string
    {
        $head = \file_get_contents($dir . '/.git/HEAD');
        if (\preg_match('~^[[:alnum:]]{40,}$~', $head)) {
            return $head; // the HEAD file contained the has itself
        }
        $gitHeadFile = \trim(\preg_replace('~ref:\s*~', '', \file_get_contents($dir . '/.git/HEAD')));
        $gitHeadFilePath = $dir . '/.git/' . $gitHeadFile;
        if (\is_readable($gitHeadFilePath)) {
            return \trim(\file_get_contents($gitHeadFilePath));
        }
        $packedRefsFilePath = $dir . '/.git/packed-refs';
        if (\is_readable($packedRefsFilePath)) {
            $packedRefs = \trim(\file_get_contents($packedRefsFilePath));
            if (preg_match('~(^|[^[:alnum:]])(?<hash>[[:alnum:]]+)\s+' . preg_quote($gitHeadFile, '~') . '($|[^[:alnum:]])~', $packedRefs, $matches)) {
                return $matches['hash'];
            }
        }
        throw new Exceptions\CanNotReadGitHead(
            "Could not read $gitHeadFilePath, in that dir are files "
            .  implode(
                ',',
                array_filter(
                    \scandir(\dirname($gitHeadFilePath), SCANDIR_SORT_NONE),
                    static fn(string $dirName) => $dirName !== '.' && $dirName !== '..'
                )
            )
        );
    }

    /**
     * @test
     */
    public function I_will_get_last_unstable_version_as_patch_version(): void
    {
        $webVersions = $this->createWebVersions();
        self::assertSame($webVersions->getLastUnstableVersion(), $webVersions->getLastPatchVersionOf($webVersions->getLastUnstableVersion()));
    }

    /**
     * @test
     */
    public function I_can_not_get_last_patch_version_for_non_existing_version(): void
    {
        $this->expectException(\Granam\Git\Exceptions\NoPatchVersionsMatch::class);
        $nonExistingVersion = '-999.999';
        $webVersions = $this->createWebVersions();
        try {
            self::assertNotContains($nonExistingVersion, $webVersions->getAllMinorVersions(), 'This version really exists?');
        } catch (\Exception $exception) {
            self::fail('No exception expected so far: ' . $exception->getMessage());
        }
        $webVersions->getLastPatchVersionOf($nonExistingVersion);
    }

    /**
     * @test
     */
    public function I_can_get_current_minor_version(): void
    {
        $branchName = 'foo.bar';
        $git = $this->createGitWithCurrentBranchName($branchName);
        /** @var WebVersions $webVersions */
        $currentWebVersion = $this->createCurrentWebVersion(null, $git);

        self::assertSame($branchName, $currentWebVersion->getCurrentMinorVersion());
    }
}
