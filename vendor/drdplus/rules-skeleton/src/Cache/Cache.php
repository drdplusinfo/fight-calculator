<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Cache;

use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\CurrentWebVersion;
use DrdPlus\RulesSkeleton\RequestPathProvider;
use Granam\Git\Exceptions\CanNotDiffDetachedBranch;
use Granam\Git\Git;
use Granam\Strict\Object\StrictObject;

abstract class Cache extends StrictObject implements CacheInterface
{
    // named parameters
    public const IN_PRODUCTION = true;
    public const NOT_IN_PRODUCTION = false;

    protected string $projectRootDir;
    private string $cacheRootDir;
    private \DrdPlus\RulesSkeleton\RequestPathProvider $requestPathProvider;
    protected array $cacheRoots = [];
    protected \DrdPlus\RulesSkeleton\CurrentWebVersion $currentWebVersion;
    private \DrdPlus\RulesSkeleton\Cache\CachingPermissionProvider $cachingPermissionProvider;
    private \DrdPlus\RulesSkeleton\Cache\ContentRelatedContextHashProvider $contentRelatedContextHashProvider;
    private \Granam\Git\Git $git;
    private \DrdPlus\RulesSkeleton\Configurations\Configuration $configuration;
    protected bool $isInProduction;

    public function __construct(
        CurrentWebVersion $currentWebVersion,
        string $projectRootDir,
        string $cacheRootDir,
        RequestPathProvider $requestPathProvider,
        CachingPermissionProvider $cachingPermissionProvider,
        ContentRelatedContextHashProvider $contentRelatedContextHashProvider,
        Git $git,
        Configuration $configuration,
        bool $isInProduction
    )
    {
        $this->currentWebVersion = $currentWebVersion;
        $this->projectRootDir = $projectRootDir;
        $this->cacheRootDir = $cacheRootDir;
        $this->requestPathProvider = $requestPathProvider;
        $this->contentRelatedContextHashProvider = $contentRelatedContextHashProvider;
        $this->git = $git;
        $this->configuration = $configuration;
        $this->isInProduction = $isInProduction;
        $this->cachingPermissionProvider = $cachingPermissionProvider;
    }

    public function getCacheDir(): string
    {
        $currentVersion = $this->currentWebVersion->getCurrentMinorVersion();
        $requestPath = $this->requestPathProvider->getRequestPath();
        if (($this->cacheRoots[$currentVersion][$requestPath] ?? null) === null) {
            $cacheRoot = rtrim($this->cacheRootDir . '/' . $currentVersion . '/' . $requestPath, '/');
            if (!\file_exists($cacheRoot)) {
                if (!@\mkdir($cacheRoot, 0775, true /* with parents */) && !\is_dir($cacheRoot)) {
                    throw new \RuntimeException('Can not create directory for page cache ' . $cacheRoot);
                }
                chmod($cacheRoot, 0775); // because umask could suppress it
            }
            $this->cacheRoots[$currentVersion][$requestPath] = $cacheRoot;
        }

        return $this->cacheRoots[$currentVersion][$requestPath];
    }

    public function isInProduction(): bool
    {
        return $this->isInProduction;
    }

    /**
     * @return bool
     * @throws \RuntimeException
     */
    public function isCacheValid(): bool
    {
        return $this->cachingPermissionProvider->isCachingAllowed() && is_readable($this->getCacheFileName());
    }

    public function getCacheId(): string
    {
        return $this->getCurrentContentHash() . '_' . $this->contentRelatedContextHashProvider->getContextHash();
    }

    private function getCacheFileName(): string
    {
        return $this->getCacheDir() . "/{$this->getCacheId()}.html";
    }

    private function getCurrentContentHash(): string
    {
        return sprintf(
            '%s_%s_%s',
            $this->currentWebVersion->getCurrentPatchVersion(),
            $this->currentWebVersion->getCurrentCommitHash(),
            $this->getChangesStamp()
        );
    }

    private function getChangesStamp(): string
    {
        return md5($this->getGitStamp() . '_' . $this->getLocalConfigurationStamp());
    }

    private function getGitStamp(): string
    {
        if ($this->isInProduction()) {
            return 'production';
        }
        $gitStatus = $this->git->getGitStatus($this->projectRootDir);
        try {
            $diffAgainstOriginMaster = $this->git->getDiffAgainstOrigin($this->projectRootDir);
        } catch (CanNotDiffDetachedBranch $exception) {
            $diffAgainstOriginMaster = [$exception->getMessage()];
        }
        $gitStatusImploded = implode($gitStatus);
        $diffAgainstOriginMasterImploded = implode($diffAgainstOriginMaster);

        return md5($gitStatusImploded . $diffAgainstOriginMasterImploded);
    }

    private function getLocalConfigurationStamp(): string
    {
        if ($this->isInProduction()) {
            return 'production';
        }
        return md5(serialize($this->configuration->getValues()));
    }

    /**
     * @return string
     * @throws \DrdPlus\RulesSkeleton\Cache\Exceptions\CanNotReadCachedContent
     */
    public function getCachedContent(): string
    {
        $cachedContent = file_get_contents($this->getCacheFileName());
        if ($cachedContent === false) {
            throw new Exceptions\CanNotReadCachedContent("Can not read cached content from '{$this->getCacheFileName()}'");
        }

        return $cachedContent;
    }

    /**
     * @param string $content
     * @throws \DrdPlus\RulesSkeleton\Cache\Exceptions\CanNotSaveContentForDebug
     * @throws \DrdPlus\RulesSkeleton\Cache\Exceptions\CanNotChangeAccessToFileWithContentForDebug
     */
    public function saveContentForDebug(string $content): void
    {
        $cacheDebugFileName = $this->getCacheDebugFileName();
        if (!file_put_contents($cacheDebugFileName, $content, \LOCK_EX)) {
            throw new Exceptions\CanNotSaveContentForDebug('Can not save content for debugging purpose into ' . $cacheDebugFileName);
        }
        if (!@chmod($cacheDebugFileName, 0664)) {
            throw new Exceptions\CanNotChangeAccessToFileWithContentForDebug(
                'Can not change access to 0644 for file with content for debug ' . $cacheDebugFileName
            );
        }
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getCacheDebugFileName(): string
    {
        return sprintf(
            '%s/%s_%s.html',
            $this->getCacheDir(),
            $this->geCacheDebugFileBaseNamePartWithoutGet(),
            $this->contentRelatedContextHashProvider->getContextHash()
        );
    }

    private function geCacheDebugFileBaseNamePartWithoutGet(): string
    {
        return 'debug_' . $this->getCurrentContentHash();
    }

    /**
     * @param string $content
     * @throws \RuntimeException
     */
    public function cacheContent(string $content): void
    {
        $cacheFileName = $this->getCacheFileName();
        file_put_contents($cacheFileName, $content, \LOCK_EX);
        chmod($cacheFileName, 0664);
        $this->clearOldCache();
    }

    /**
     * @throws \RuntimeException
     */
    private function clearOldCache(): void
    {
        $foldersToSkip = ['.', '..', '.gitignore'];
        $cacheDir = $this->getCacheDir();

        $currentContentHash = $this->getCurrentContentHash();
        foreach (scandir($cacheDir, \SCANDIR_SORT_NONE) as $folder) {
            if (in_array($folder, $foldersToSkip, true)) {
                continue;
            }
            if (strpos($folder, $currentContentHash) !== false) { // that file is valid
                continue;
            }
            if (is_dir($cacheDir . '/' . $folder)) { // dir with cached sub-routes
                continue;
            }
            unlink($cacheDir . '/' . $folder);
        }
    }
}
