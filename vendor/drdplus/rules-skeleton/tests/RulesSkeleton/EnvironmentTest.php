<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_detect_cli_request()
    {
        $environment = new Environment('stone');
        self::assertFalse($environment->isCliRequest());
        $environment = new Environment('cli');
        self::assertTrue($environment->isCliRequest());
    }

    /**
     * @test
     */
    public function I_can_get_php_sapi(): void
    {
        $environment = new Environment('stone');
        self::assertSame('stone', $environment->getPhpSapi());
    }

    /**
     * @test
     * @dataProvider provideValuesForDevelopmentDetection
     * @param string $phpSapi
     * @param string|null $projectEnvironment
     * @param bool $expectedAsDev
     */
    public function I_can_control_development_environment_by_env_variable(string $phpSapi, ?string $projectEnvironment, bool $expectedAsDev)
    {
        $environmentWithoutProject = new Environment($phpSapi, $projectEnvironment);
        self::assertSame($expectedAsDev, $environmentWithoutProject->isOnDevEnvironment());
    }

    public function provideValuesForDevelopmentDetection(): array
    {
        return [
            'project environment as NULL' => ['foo', null, false],
            'project environment as strange string' => ['foo', 'unknown', false],
            'project environment as shortest dev name' => ['foo', 'dev', true],
            'project environment as long dev name' => ['foo', 'development', true],
            'project environment as uppercase short dev name' => ['foo', 'DEV', true],
            'project environment as capitalized long dev name' => ['foo', 'Development', true],
        ];
    }

    /**
     * @test
     */
    public function I_can_detect_localhost()
    {
        $environmentWithoutRemoteAddress = new Environment('foo');
        self::assertFalse($environmentWithoutRemoteAddress->isOnLocalhost(), 'Localhost should not be detected');
        $environmentWithRemoteAddress = new Environment('foo', null, '999.999.999.999');
        self::assertFalse($environmentWithRemoteAddress->isOnLocalhost());
        $environmentWithLocalAddress = new Environment('foo', null, '127.0.0.1');
        self::assertTrue($environmentWithLocalAddress->isOnLocalhost());
    }

    /**
     * @test
     * @dataProvider provideEnvironmentToDetectProduction
     * @param bool $expectedProduction
     * @param string $phpSapi
     * @param string|null $projectEnvironment
     * @param string|null $remoteAddr
     * @param string|null $forcedMode
     */
    public function I_can_find_out_if_I_am_in_production(
        bool $expectedProduction,
        string $phpSapi,
        ?string $projectEnvironment,
        ?string $remoteAddr,
        ?string $forcedMode
    ): void
    {
        self::assertSame(
            $expectedProduction,
            (new Environment($phpSapi, $projectEnvironment, $remoteAddr, $forcedMode))->isInProduction()
        );
    }

    private const NOT_EXPECTED_PRODUCTION = false;
    private const EXPECTED_PRODUCTION = true;
    private const NOT_FORCED_MODE = null;
    private const FORCED_PROD = 'PRODUCTION';
    private const CLI_PHP_SAPI = 'cli';
    private const PHPDBG_PHP_SAPI = 'phpdbg';
    private const EMBED_PHP_SAPI = 'embed';
    private const PHP_FPM_PHP_SAPI = 'fpm-fcgi';
    private const DEVELOPMENT = 'Development';
    private const LOCALHOST_IP = '127.0.0.1';

    public function provideEnvironmentToDetectProduction(): array
    {
        return [
            'production' => [self::EXPECTED_PRODUCTION, self::PHP_FPM_PHP_SAPI, null, null, self::NOT_FORCED_MODE],
            'forced production on production' => [self::EXPECTED_PRODUCTION, self::PHP_FPM_PHP_SAPI, null, null, self::FORCED_PROD],

            'dev' => [self::NOT_EXPECTED_PRODUCTION, self::PHP_FPM_PHP_SAPI, self::DEVELOPMENT, null, self::NOT_FORCED_MODE],
            'forced production on dev' => [self::EXPECTED_PRODUCTION, self::PHP_FPM_PHP_SAPI, self::DEVELOPMENT, null, self::FORCED_PROD],

            'cli' => [self::NOT_EXPECTED_PRODUCTION, self::CLI_PHP_SAPI, null, null, self::NOT_FORCED_MODE],
            'forced production on cli' => [self::EXPECTED_PRODUCTION, self::CLI_PHP_SAPI, null, null, self::FORCED_PROD],

            'phpdbg' => [self::NOT_EXPECTED_PRODUCTION, self::PHPDBG_PHP_SAPI, null, null, self::NOT_FORCED_MODE],
            'forced production on debug (cli)' => [self::EXPECTED_PRODUCTION, self::PHPDBG_PHP_SAPI, null, null, self::FORCED_PROD],

            'embed' => [self::NOT_EXPECTED_PRODUCTION, self::EMBED_PHP_SAPI, null, null, self::NOT_FORCED_MODE],
            'forced production on embed (cli)' => [self::EXPECTED_PRODUCTION, self::EMBED_PHP_SAPI, null, null, self::FORCED_PROD],

            'localhost' => [self::NOT_EXPECTED_PRODUCTION, self::PHP_FPM_PHP_SAPI, null, self::LOCALHOST_IP, self::NOT_FORCED_MODE],
            'forced production on localhost' => [self::EXPECTED_PRODUCTION, self::PHP_FPM_PHP_SAPI, null, self::LOCALHOST_IP, self::FORCED_PROD],

            'dev cli' => [self::NOT_EXPECTED_PRODUCTION, self::CLI_PHP_SAPI, self::DEVELOPMENT, null, self::NOT_FORCED_MODE],
            'forced production on dev cli' => [self::EXPECTED_PRODUCTION, self::CLI_PHP_SAPI, self::DEVELOPMENT, null, self::FORCED_PROD],

            'dev localhost' => [self::NOT_EXPECTED_PRODUCTION, self::PHP_FPM_PHP_SAPI, self::DEVELOPMENT, self::LOCALHOST_IP, self::NOT_FORCED_MODE],
            'forced production on dev localhost' => [self::EXPECTED_PRODUCTION, self::PHP_FPM_PHP_SAPI, self::DEVELOPMENT, self::LOCALHOST_IP, self::FORCED_PROD],
        ];
    }

}
