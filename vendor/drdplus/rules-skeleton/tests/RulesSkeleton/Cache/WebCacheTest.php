<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Cache;

use DrdPlus\RulesSkeleton\Cache\Cache;
use DrdPlus\RulesSkeleton\Cache\WebCache;
use DrdPlus\RulesSkeleton\CurrentWebVersion;
use DrdPlus\RulesSkeleton\Request;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Granam\String\StringTools;
use Mockery\MockInterface;

class WebCacheTest extends AbstractContentTest
{
    protected ?string $temporaryRootDir = null;

    protected function tearDown(): void
    {
        parent::tearDown();
        if ($this->temporaryRootDir) {
            \exec('rm -fr ' . \escapeshellarg($this->temporaryRootDir));
        }
    }

    /**
     * @test
     * @dataProvider provideVersions
     * @param string $version
     */
    public function I_will_get_cache_dir_depending_on_current_version(string $version): void
    {
        // using temporary NON-existing dir to use more code
        $dirs = $this->createDirs($this->getTemporaryRootDir());
        $webCacheClass = $this->getWebCacheClass();
        $cacheSubDir = \uniqid(__FUNCTION__, true);
        /** @var WebCache $cache */
        $cache = new $webCacheClass(
            $this->createCurrentWebVersionMock($version),
            $dirs,
            $cacheSubDir,
            $this->getRequest(),
            $this->getRequestCachingPermissionProvider(),
            $this->getRequestHashProvider(),
            $this->getGit(),
            $this->getConfiguration(),
            Cache::NOT_IN_PRODUCTION
        );
        self::assertSame($dirs->getCacheRoot() . '/web/' . $cacheSubDir . '/' . $version, $cache->getCacheDir());
    }

    /**
     * @param string $currentMinorVersion
     * @return CurrentWebVersion|MockInterface
     */
    private function createCurrentWebVersionMock(string $currentMinorVersion): CurrentWebVersion
    {
        $currentWebVersions = $this->mockery($this->getCurrentWebVersionClass());
        $currentWebVersions->shouldReceive('getCurrentMinorVersion')
            ->andReturn($currentMinorVersion);

        return $currentWebVersions;
    }

    protected function getTemporaryRootDir(): string
    {
        if ($this->temporaryRootDir === null) {
            $this->temporaryRootDir = \sys_get_temp_dir() . '/' . \uniqid(StringTools::getClassBaseName(static::class), true);
        }

        return $this->temporaryRootDir;
    }

    public function provideVersions(): array
    {
        return [
            ['master'],
            ['9.8.7'],
        ];
    }

    /**
     * @test
     */
    public function I_will_get_cached_content_if_available(): void
    {
        $request = $this->createRequestPartialMock();
        $cacheSubDir = \uniqid(__FUNCTION__, true);
        $webCacheClass = $this->getWebCacheClass();
        /** @var WebCache $cache */
        $cache = new $webCacheClass(
            $this->getCurrentWebVersion(),
            $this->getDirs(),
            $cacheSubDir,
            $request,
            $this->createRequestCachingPermissionProvider($request),
            $this->createRequestHashProvider($request),
            $this->getGit(),
            $this->getConfiguration(),
            Cache::NOT_IN_PRODUCTION
        );
        self::assertFalse($cache->isCacheValid(), 'Nothing should be cached so far');

        $cache->cacheContent($content = 'foo of bar over baz!');
        self::assertTrue($cache->isCacheValid(), 'Expected content to be cached now');
        self::assertSame($content, $cache->getCachedContent());

        $request->shouldReceive('getRequestPath')->andReturn('/different-route');
        self::assertFalse($cache->isCacheValid(), 'Nothing should be cached due to different route');

        $cache->cacheContent($content);
        self::assertTrue($cache->isCacheValid(), 'Expected content to be cached now');
        self::assertSame($content, $cache->getCachedContent());
    }

    /**
     * @return Request|MockInterface
     */
    private function createRequestPartialMock(): Request
    {
        $request = $this->mockery($this->getRequestClass());
        $request->makePartial();
        return $request;
    }

    /**
     * @test
     * @dataProvider provideCacheParameter
     * @param string|null $cacheParameter
     * @param bool $expectedCacheValid
     */
    public function I_can_disable_cache(?string $cacheParameter, bool $expectedCacheValid): void
    {
        $request = $this->createRequest([Request::CACHE => $cacheParameter]);
        $cacheSubDir = \uniqid(__FUNCTION__, true);
        $webCacheClass = $this->getWebCacheClass();
        /** @var WebCache $cache */
        $cache = new $webCacheClass(
            $this->getCurrentWebVersion(),
            $this->getDirs(),
            $cacheSubDir,
            $request,
            $this->createRequestCachingPermissionProvider($request),
            $this->createRequestHashProvider($request),
            $this->getGit(),
            $this->getConfiguration(),
            Cache::NOT_IN_PRODUCTION
        );

        self::assertFalse($cache->isCacheValid(), 'Nothing should be cached so far');

        $cache->cacheContent($content = 'pocked world');
        self::assertSame($expectedCacheValid, $cache->isCacheValid());

        self::assertSame($content, $cache->getCachedContent(), 'Expected content to be cached in any case');
    }

    public function provideCacheParameter(): array
    {
        return [
            'null as cache parameter' => [null, true],
            'nonsense as cache parameter' => ['nonsense', true],
            'zero as cache parameter' => ['0', false],
            'disable as cache parameter' => ['disable', false],
            'disabled as cache parameter' => ['disabled', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideCacheIrrelevantParameters
     * @param array $cacheIrrelevantParameters
     */
    public function I_will_get_same_cached_content_for_cache_irrelevant_parameters_as_for_full(array $cacheIrrelevantParameters): void
    {
        $contentIrrelevantParametersFilter = $this->getServicesContainer()->getContentIrrelevantParametersFilter();
        self::assertSame(
            [],
            $contentIrrelevantParametersFilter->filterContentIrrelevantParameters($cacheIrrelevantParameters),
            'Expected every parameter to be irrelevant for cache (and therefore page content)'
        );

        $webCacheClass = $this->getWebCacheClass();
        $cacheSubDir = \uniqid(__FUNCTION__, true);
        /** @var Cache $cacheWithoutTrial */
        $cacheWithoutTrial = new $webCacheClass(
            $this->getCurrentWebVersion(),
            $this->getDirs(),
            $cacheSubDir,
            $this->getRequest(),
            $this->getRequestCachingPermissionProvider(),
            $this->createRequestHashProvider(null, null, $contentIrrelevantParametersFilter),
            $this->getGit(),
            $this->getConfiguration(),
            Cache::NOT_IN_PRODUCTION
        );

        self::assertFalse($cacheWithoutTrial->isCacheValid(), 'Nothing should be cached so far');

        $cacheWithoutTrial->cacheContent($content = 'foo of bar over baz!');
        self::assertTrue($cacheWithoutTrial->isCacheValid(), 'Expected content to be cached now');
        self::assertSame($content, $cacheWithoutTrial->getCachedContent());

        $request = $this->createRequest($cacheIrrelevantParameters);
        /** @var Cache $cacheWithTrialRequest */
        $cacheWithTrialRequest = new $webCacheClass(
            $this->getCurrentWebVersion(),
            $this->getDirs(),
            $cacheSubDir, // same sub-dir
            $request,
            $this->createRequestCachingPermissionProvider($request),
            $this->createRequestHashProvider($request, null, $contentIrrelevantParametersFilter),
            $this->getGit(),
            $this->getConfiguration(),
            Cache::NOT_IN_PRODUCTION
        );
        self::assertTrue($cacheWithTrialRequest->isCacheValid(), 'Expected content to be already cached');
        self::assertSame($content, $cacheWithTrialRequest->getCachedContent());
    }

    public function provideCacheIrrelevantParameters(): array
    {
        return [
            Request::TRIAL => [[Request::TRIAL => 1]],
            'fbclid' => [['fbclid' => 'IwAR0WtjBi1tamu7ww-tD094difpqH37IMF3U-AqAcC5t-WQqS7wvcGL-5LaQ']],
        ];
    }
}
