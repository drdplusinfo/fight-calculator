<?php declare(strict_types=1);

namespace Granam\Tests\WebContentBuilder;

use Granam\Tests\WebContentBuilder\Partials\AbstractContentTest;
use Granam\WebContentBuilder\CookiesBag;

class CookiesBagTest extends AbstractContentTest
{
    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_set_get_and_delete_cookie(): void
    {
        $cookiesBagClass = static::getSutClass();
        /** @var CookiesBag $cookiesBag */
        $cookiesBag = new $cookiesBagClass();
        self::assertNull($cookiesBag->getCookie('foo'));
        self::assertTrue($cookiesBag->setCookie('foo', 'bar'));
        self::assertSame('bar', $cookiesBag->getCookie('foo'));
        self::assertSame('bar', $_COOKIE['foo'] ?? '');
        self::assertTrue($cookiesBag->deleteCookie('foo'));
        self::assertNull($cookiesBag->getCookie('foo'));
        self::assertArrayNotHasKey('foo', $_COOKIE, 'Cookie should be removed from global array as well');
    }
}
