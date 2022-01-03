<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Configurations;

use DrdPlus\RulesSkeleton\Configurations\RoutedDirs;
use DrdPlus\RulesSkeleton\RouteMatchingPathProvider;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Mockery\MockInterface;

class RoutedDirsTest extends AbstractContentTest
{
    /**
     * @test
     */
    public function I_can_use_it(): void
    {
        $dirsClass = static::getSutClass();
        /** @var RoutedDirs $routedDirs */
        $routedDirs = new $dirsClass('foo', $this->createRouteMatchingPathProvider('\\bar'));
        self::assertSame('foo/web/bar', $routedDirs->getWebRoot());
    }

    /**
     * @param string $path
     * @return RouteMatchingPathProvider|MockInterface
     */
    private function createRouteMatchingPathProvider(string $path): RouteMatchingPathProvider
    {
        $routeMatchingPathProvider = $this->mockery(RouteMatchingPathProvider::class);
        $routeMatchingPathProvider->shouldReceive('getMatchingPath')
            ->andReturn($path);

        return $routeMatchingPathProvider;
    }
}
