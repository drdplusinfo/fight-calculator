<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\CookiesService;
use DrdPlus\RulesSkeleton\Request;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Tests\DrdPlus\RulesSkeleton\Partials\ClassesTrait;

class CookiesServiceTest extends AbstractContentTest
{
    use ClassesTrait;

    /**
     * @test
     */
    public function I_can_set_get_overwrite_and_delete_cookie(): void
    {
        $cookiesServiceClass = $this->getCookiesServiceClass();
        $requestClass = $this->getRequestClass();
        /** @var Request $request */
        $request = new $requestClass($this->getBot(), $this->getEnvironment(), [], [], [], []);
        /** @var CookiesService $cookiesService */
        $cookiesService = new $cookiesServiceClass($request);

        self::assertNull($request->getValueFromCookie('foo'));
        self::assertNull($cookiesService->getCookie('foo'));

        self::assertTrue($cookiesService->setCookie('foo', 'bar'));
        self::assertSame('bar', $cookiesService->getCookie('foo'));
        self::assertSame('bar', $request->getValueFromCookie('foo'));

        self::assertTrue($cookiesService->setCookie('foo', 'baz'));
        self::assertSame('baz', $cookiesService->getCookie('foo'));
        self::assertSame('baz', $request->getValueFromCookie('foo'));

        self::assertTrue($cookiesService->deleteCookie('foo'));
        self::assertNull($cookiesService->getCookie('foo'));
        self::assertNull($request->getValueFromCookie('foo'));
    }
}
