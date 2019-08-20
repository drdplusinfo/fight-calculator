<?php declare(strict_types=1);

namespace DrdPlus\Tests\RulesSkeleton;

use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\Tests\RulesSkeleton\Partials\AbstractContentTest;
use Mockery\MockInterface;

/**
 * @backupGlobals enabled
 */
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
     */
    public function I_can_detect_czech_seznam_bot(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
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
     */
    public function I_do_not_get_non_bot_browsers_marked_as_bots(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
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
     */
    public function I_can_get_current_url_even_if_query_string_is_not_set(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
        unset($_SERVER['QUERY_STRING']);
        self::assertSame('/', $request->getCurrentUrl());
    }

    /**
     * @test
     */
    public function I_can_get_current_url_with_updated_query_parameters(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
        $_GET = ['foo' => 123, 'bar' => 456];
        self::assertSame('/?foo=0&bar=456&baz=OK', $request->getCurrentUrl(['foo' => false, 'baz' => 'OK']));
    }

    /**
     * @test
     */
    public function I_can_get_current_url_with_updated_query_parameters_even_if_get_is_not_set(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
        unset($_GET);
        $currentUrl = $request->getCurrentUrl(['foo' => true]);
        global $_GET; // because backup globals do not works for unset global
        $_GET = [];
        self::assertSame('/?foo=1', $currentUrl);
    }

    /**
     * @test
     */
    public function I_can_get_current_url_with_updated_query_parameters_and_removed_unwanted(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
        $_GET = ['foo' => 123, 'bar' => 456, 'baz' => 789];
        self::assertSame(
            '/?foo=0&baz=789',
            $request->getCurrentUrl(['foo' => false], ['bar'])
        );
    }

    /**
     * @test
     */
    public function I_can_get_current_url_without_automatic_parameters(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
        $_GET = ['foo' => 'ok', 'bar' => 'not ok', Request::TRIAL_EXPIRED_AT => time(), Request::TRIAL => '1', Request::CACHE => '1'];
        self::assertSame(
            sprintf('/?foo=nice&%s=1&%s=1', Request::TRIAL, Request::CACHE),
            $request->getCurrentUrlWithoutAutomaticValues(['foo' => 'nice'], ['bar'])
        );
    }

    /**
     * @test
     */
    public function I_can_get_value_from_get(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
        unset($_GET);
        self::assertNull($request->getValueFromGet('foo'));
        global $_GET; // because backup globals do not works for unset global
        $_GET = ['foo' => 'bar'];
        self::assertSame('bar', $request->getValueFromGet('foo'));

    }

    /**
     * @test
     */
    public function I_can_get_value_from_post(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
        unset($_POST);
        self::assertNull($request->getValueFromPost('foo'));
        global $_POST; // because backup globals do not works for unset global
        $_POST = ['foo' => 'bar'];
        self::assertSame('bar', $request->getValueFromPost('foo'));
    }

    /**
     * @test
     */
    public function I_can_get_value_from_cookie(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
        unset($_COOKIE);
        self::assertNull($request->getValueFromCookie('foo'));
        global $_COOKIE; // because backup globals do not works for unset global
        $_COOKIE = ['foo' => 'bar'];
        self::assertSame('bar', $request->getValueFromCookie('foo'));
    }

    /**
     * @test
     */
    public function I_can_get_value_from_request_with_priority_post_get_cookie(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
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
     * @dataProvider provideTablesIdsParameterName
     * @param string $parameterName
     */
    public function I_can_get_requested_tables_ids(string $parameterName): void
    {
        self::assertSame([], (new Request($this->getBot(), $this->getEnvironment()))->getRequestedTablesIds());
        $_GET[$parameterName] = '    ';
        self::assertSame([], (new Request($this->getBot(), $this->getEnvironment()))->getRequestedTablesIds());
        $_GET[$parameterName] = 'foo';
        self::assertSame(['foo'], (new Request($this->getBot(), $this->getEnvironment()))->getRequestedTablesIds());
        $_GET[$parameterName] .= ',bar,baz';
        self::assertSame(['foo', 'bar', 'baz'], (new Request($this->getBot(), $this->getEnvironment()))->getRequestedTablesIds());
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
     */
    public function I_can_get_path_info(): void
    {
        $_SERVER['PATH_INFO'] = null;
        $request = new Request($this->getBot(), $this->getEnvironment());
        self::assertSame('', $request->getPathInfo());
        $_SERVER['PATH_INFO'] = 'foo/bar';
        self::assertSame('foo/bar', $request->getPathInfo());
    }

    /**
     * @test
     */
    public function I_can_get_current_path(): void
    {
        $_SERVER['REQUEST_URI'] = null;
        $request = new Request($this->getBot(), $this->getEnvironment());
        self::assertSame('/', $request->getPath());
        $_SERVER['REQUEST_URI'] = '/foo/bar';
        self::assertSame('/foo/bar', $request->getPath());
    }

    /**
     * @test
     */
    public function I_can_get_query_string(): void
    {
        $_SERVER['QUERY_STRING'] = null;
        $request = new Request($this->getBot(), $this->getEnvironment());
        self::assertSame('', $request->getQueryString());
        $_SERVER['QUERY_STRING'] = 'foo=bar';
        self::assertSame('foo=bar', $request->getQueryString());
    }

    /**
     * @test
     */
    public function I_can_find_out_if_request_comes_from_cli(): void
    {
        $request = new Request($this->getBot(), $this->createEnvironment('paper'));
        self::assertFalse($request->isCliRequest());
        $request = new Request($this->getBot(), $this->createEnvironment('cli'));
        self::assertTrue($request->isCliRequest());
    }

    /**
     * @param string $phpSapi
     * @return Environment|MockInterface
     */
    private function createEnvironment(string $phpSapi): Environment
    {
        $environment = $this->mockery(Environment::class);
        $environment->shouldReceive('getPhpSapi')
            ->andReturn($phpSapi);
        $environment->makePartial();
        return $environment;
    }

    /**
     * @test
     */
    public function I_can_find_out_if_trial_just_expired(): void
    {
        $request = new Request($this->getBot(), $this->getEnvironment());
        self::assertFalse($request->trialJustExpired());
        $_GET[Request::TRIAL_EXPIRED_AT] = time() + 10;
        self::assertFalse($request->trialJustExpired());
        $_GET[Request::TRIAL_EXPIRED_AT] = time() - 10;
        self::assertTrue($request->trialJustExpired());
    }

    /**
     * @test
     */
    public function I_will_get_homepage_on_multiple_slashes(): void
    {
        $router = new Request($this->getBot(), $this->getEnvironment());
        $_SERVER['REQUEST_URI'] = '//';
        self::assertSame('/', $router->getPath());
    }

}