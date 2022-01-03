<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\HomepageDetector;
use DrdPlus\RulesSkeleton\RouteMatchingPathProvider;
use Granam\TestWithMockery\TestWithMockery;
use Mockery\MockInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class HomepageDetectorTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_find_out_if_is_homepage_requested()
    {
        $homepageDetectorWithNothing = new HomepageDetector($this->createPathProvider(''));
        self::assertTrue($homepageDetectorWithNothing->isHomepageRequested());

        $homepageDetectorWithSlash = new HomepageDetector($this->createPathProvider('/'));
        self::assertTrue($homepageDetectorWithSlash->isHomepageRequested());

        $homepageDetectorWithSomeRoute = new HomepageDetector($this->createPathProvider('/foo'));
        self::assertFalse($homepageDetectorWithSomeRoute->isHomepageRequested());
    }

    /**
     * @param string $path
     * @return RouteMatchingPathProvider|MockInterface
     */
    private function createPathProvider(string $path): RouteMatchingPathProvider
    {
        $pathProvider = $this->mockery(RouteMatchingPathProvider::class);
        $pathProvider->shouldReceive('getMatchingPath')
            ->andReturn($path);
        return $pathProvider;
    }

    /**
     * @test
     * @dataProvider provideExceptionOfPathProvider
     * @param ExceptionInterface $exception
     */
    public function I_will_get_false_if_route_is_not_found(ExceptionInterface $exception)
    {
        $homepageDetectorWithNothing = new HomepageDetector($this->createBrokenPathProvider($exception));
        self::assertFalse($homepageDetectorWithNothing->isHomepageRequested());
    }

    public function provideExceptionOfPathProvider(): array
    {
        return [
            [new RouteNotFoundException()],
            [new ResourceNotFoundException()],
        ];
    }

    /**
     * @param ExceptionInterface $exception
     * @return RouteMatchingPathProvider|MockInterface
     */
    private function createBrokenPathProvider(ExceptionInterface $exception): RouteMatchingPathProvider
    {
        $pathProvider = $this->mockery(RouteMatchingPathProvider::class);
        $pathProvider->shouldReceive('getMatchingPath')
            ->andThrow($exception);
        return $pathProvider;
    }
}
