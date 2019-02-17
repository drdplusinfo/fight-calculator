<?php
declare(strict_types=1);

namespace DrdPlus\Tests\RulesSkeleton;

use DrdPlus\RulesSkeleton\Request;
use DrdPlus\Tests\RulesSkeleton\Partials\AbstractContentTest;

class RequestTest extends AbstractContentTest
{
    public static function getCrawlerUserAgents(): array
    {
        return [
            'Mozilla/5.0 (compatible; SeznamBot/3.2; +http://napoveda.seznam.cz/en/seznambot-intro/)',
            'User-Agent: Mozilla/5.0 (compatible; SeznamBot/3.2-test4; +http://napoveda.seznam.cz/en/seznambot-intro/)',
            'Googlebot',
        ];
    }

    public static function getNonCrawlerUserAgents(): array
    {
        return [
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:60.0) Gecko/20100101 Firefox/60.0', // Firefox
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.62 Safari/537.36' // Chrome
        ];
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_detect_czech_seznam_bot(): void
    {
        $request = new Request($this->getBot());
        foreach (static::getCrawlerUserAgents() as $crawlerUserAgent) {
            self::assertTrue(
                $request->isVisitorBot($crawlerUserAgent),
                'Directly passed crawler has not been recognized: ' . $crawlerUserAgent
            );
            $_SERVER['HTTP_USER_AGENT'] = $crawlerUserAgent;
            self::assertTrue(
                $request->isVisitorBot(),
                'Crawler has not been recognized from HTTP_USER_AGENT: ' . $crawlerUserAgent
            );
        }
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_do_not_get_non_bot_browsers_marked_as_bots(): void
    {
        $request = new Request($this->getBot());
        foreach (static::getNonCrawlerUserAgents() as $nonCrawlerUserAgent) {
            self::assertFalse(
                $request->isVisitorBot($nonCrawlerUserAgent),
                'Directly passed browser has been wrongly marked as a bot: ' . $nonCrawlerUserAgent
            );
            $_SERVER['HTTP_USER_AGENT'] = $nonCrawlerUserAgent;
            self::assertFalse(
                $request->isVisitorBot(),
                'Browser has been wrongly marked as a bot from HTTP_USER_AGENT: ' . $nonCrawlerUserAgent
            );
        }
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_get_current_url_even_if_query_string_is_not_set(): void
    {
        $request = new Request($this->getBot());
        unset($_SERVER['QUERY_STRING']);
        self::assertSame('', $request->getCurrentUrl());
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_get_current_url_with_updated_query_parameters(): void
    {
        $request = new Request($this->getBot());
        $_GET = ['foo' => 123, 'bar' => 456];
        self::assertSame('?foo=0&bar=456&baz=OK', $request->getCurrentUrl(['foo' => false, 'baz' => 'OK']));
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_get_current_url_with_updated_query_parameters_even_if_get_is_not_set(): void
    {
        $request = new Request($this->getBot());
        unset($_GET);
        $currentUrl = $request->getCurrentUrl(['foo' => true]);
        global $_GET; // because backup globals do not works for unset global
        $_GET = [];
        self::assertSame('?foo=1', $currentUrl);
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_get_value_from_get(): void
    {
        $request = new Request($this->getBot());
        unset($_GET);
        self::assertNull($request->getValueFromGet('foo'));
        global $_GET; // because backup globals do not works for unset global
        $_GET = ['foo' => 'bar'];
        self::assertSame('bar', $request->getValueFromGet('foo'));

    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_get_value_from_post(): void
    {
        $request = new Request($this->getBot());
        unset($_POST);
        self::assertNull($request->getValueFromPost('foo'));
        global $_POST; // because backup globals do not works for unset global
        $_POST = ['foo' => 'bar'];
        self::assertSame('bar', $request->getValueFromPost('foo'));
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_get_value_from_cookie(): void
    {
        $request = new Request($this->getBot());
        unset($_COOKIE);
        self::assertNull($request->getValueFromCookie('foo'));
        global $_COOKIE; // because backup globals do not works for unset global
        $_COOKIE = ['foo' => 'bar'];
        self::assertSame('bar', $request->getValueFromCookie('foo'));
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_get_value_from_request_with_priority_post_get_cookie(): void
    {
        $request = new Request($this->getBot());
        unset($_POST, $_GET, $_COOKIE);
        self::assertNull($request->getValue('foo'));
        global $_POST, $_GET, $_COOKIE; // because backup globals do not works for unset global
        $_POST = ['foo' => 'from post'];
        $_GET = ['foo' => 'from get'];
        $_COOKIE = ['foo' => 'from cookie'];
        self::assertSame('from post', $request->getValue('foo'));
        unset($_POST['foo']);
        self::assertSame('from get', $request->getValue('foo'));
        unset($_GET['foo']);
        self::assertSame('from cookie', $request->getValue('foo'));
        unset($_COOKIE['foo']);
        self::assertNull($request->getValue('foo'));
    }

    /**
     * @test
     * @backupGlobals enabled
     * @dataProvider provideTablesIdsParameterName
     * @param string $parameterName
     */
    public function I_can_get_requested_tables_ids(string $parameterName): void
    {
        self::assertSame([], (new Request($this->getBot()))->getRequestedTablesIds());
        $_GET[$parameterName] = '    ';
        self::assertSame([], (new Request($this->getBot()))->getRequestedTablesIds());
        $_GET[$parameterName] = 'foo';
        self::assertSame(['foo'], (new Request($this->getBot()))->getRequestedTablesIds());
        $_GET[$parameterName] .= ',bar,baz';
        self::assertSame(['foo', 'bar', 'baz'], (new Request($this->getBot()))->getRequestedTablesIds());
        unset($_GET[$parameterName]); // to avoid using this in next iteration as @backupGlobals does not work
    }

    public function provideTablesIdsParameterName(): array
    {
        return [
            [Request::TABLES],
            [Request::TABULKY],
        ];
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_get_path_info(): void
    {
        $_SERVER['PATH_INFO'] = null;
        $request = new Request($this->getBot());
        self::assertSame('', $request->getPathInfo());
        $_SERVER['PATH_INFO'] = 'foo/bar';
        self::assertSame('foo/bar', $request->getPathInfo());
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_get_query_string(): void
    {
        $_SERVER['QUERY_STRING'] = null;
        $request = new Request($this->getBot());
        self::assertSame('', $request->getQueryString());
        $_SERVER['QUERY_STRING'] = 'foo=bar';
        self::assertSame('foo=bar', $request->getQueryString());
    }

    /**
     * @test
     */
    public function I_can_get_php_sapi(): void
    {
        $request = new Request($this->getBot());
        self::assertSame(\PHP_SAPI, $request->getPhpSapi());
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_find_out_if_trial_just_expired(): void
    {
        $request = new Request($this->getBot());
        self::assertFalse($request->trialJustExpired());
        $_GET[Request::TRIAL_EXPIRED_AT] = time() + 10;
        self::assertFalse($request->trialJustExpired());
        $_GET[Request::TRIAL_EXPIRED_AT] = time() - 10;
        self::assertTrue($request->trialJustExpired());
    }
}