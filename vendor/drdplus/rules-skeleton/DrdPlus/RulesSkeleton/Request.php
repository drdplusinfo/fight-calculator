<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use DeviceDetector\Parser\Bot;
use Granam\Strict\Object\StrictObject;

class Request extends StrictObject
{
    public const VERSION = 'version';
    public const UPDATE = 'update';
    public const CACHE = 'cache';
    public const DISABLE = 'disable';
    public const TABLES = 'tables';
    public const TABULKY = 'tabulky';
    public const CONFIRM = 'confirm';
    public const PDF = 'pdf';
    // trial
    public const TRIAL = 'trial';
    public const TRIAL_EXPIRED_AT = 'trial_expired_at';

    /** @var Bot */
    private $botParser;
    /** @var Environment */
    private $environment;

    public function __construct(Bot $botParser, Environment $environment)
    {
        $this->botParser = $botParser;
        $this->environment = $environment;
    }

    public function getServerUrl(): string
    {
        $protocol = 'http';
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        } elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $protocol = 'https';
        } elseif (!empty($_SERVER['REQUEST_SCHEME'])) {
            $protocol = $_SERVER['REQUEST_SCHEME'];
        }
        if (empty($_SERVER['SERVER_NAME'])) {
            return '';
        }
        $port = 80;
        if (!empty($_SERVER['SERVER_PORT']) && \is_numeric($_SERVER['SERVER_PORT'])) {
            $port = (int)$_SERVER['SERVER_PORT'];
        }
        $portString = $port === 80 || $port === 443
            ? ''
            : (':' . $port);

        return "{$protocol}://{$_SERVER['SERVER_NAME']}{$portString}";
    }

    public function isVisitorBot(string $userAgent = null): bool
    {
        $this->botParser->setUserAgent($userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? '');
        $this->botParser->discardDetails();

        return (bool)$this->botParser->parse();
    }

    public function getCurrentUrl(array $overwriteParameters = [], array $excludeParameters = []): string
    {
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        if ($overwriteParameters === [] && $excludeParameters === []) {
            return $url;
        }
        $path = parse_url($url, PHP_URL_PATH);
        $queryParameters = $_GET ?? [];
        foreach ($excludeParameters as $excludeParameter) {
            unset($queryParameters[$excludeParameter]);
        }
        $queryParameters = \array_merge($queryParameters ?? [], $overwriteParameters);

        return $path . '?' . \http_build_query($queryParameters);
    }

    public function getCurrentUrlWithoutAutomaticValues(array $overwriteParameters = [], array $excludeParameters = []): string
    {
        $excludeParameters[] = self::TRIAL_EXPIRED_AT;
        return $this->getCurrentUrl($overwriteParameters, $excludeParameters);
    }

    public function getPath(): string
    {
        $requestUri = preg_replace('~/{2,}~', '/', $_SERVER['REQUEST_URI'] ?? '/');
        return parse_url($requestUri, PHP_URL_PATH);
    }

    /**
     * @param string $name
     * @return null|string|array
     */
    public function getValue(string $name)
    {
        return $_POST[$name] ?? $_GET[$name] ?? $_COOKIE[$name] ?? null;
    }

    public function isCliRequest(): bool
    {
        return $this->environment->isCliRequest();
    }

    public function getValuesFromGet(): array
    {
        return $_GET ?? [];
    }

    public function getValueFromPost(string $name)
    {
        return $_POST[$name] ?? null;
    }

    public function getValueFromGet(string $name)
    {
        return $_GET[$name] ?? null;
    }

    public function getValueFromCookie(string $name)
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * @return array|string[]
     */
    public function getRequestedTablesIds(): array
    {
        $wantedTableIds = \array_map(
            function (string $id) {
                return \trim($id);
            },
            \explode(',', $_GET[self::TABLES] ?? $_GET[self::TABULKY] ?? '')
        );

        return \array_filter(
            $wantedTableIds,
            function (string $id) {
                return $id !== '';
            }
        );
    }

    public function areRequestedTables(): bool
    {
        return $this->getValueFromGet(self::TABLES) !== null || $this->getValueFromGet(self::TABULKY) !== null;
    }

    public function getPathInfo(): string
    {
        return $_SERVER['PATH_INFO'] ?? '';
    }

    public function getQueryString(): string
    {
        return $_SERVER['QUERY_STRING'] ?? '';
    }

    public function isRequestedPdf(): bool
    {
        return $this->getQueryString() === self::PDF || $this->getValueFromGet(self::PDF) !== null;
    }

    public function trialJustExpired(): bool
    {
        return !empty($_GET[static::TRIAL_EXPIRED_AT]) && ((int)$_GET[static::TRIAL_EXPIRED_AT]) <= \time();
    }
}