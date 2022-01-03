<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Partials;

use DeviceDetector\Parser\Bot;
use DrdPlus\RulesSkeleton\Cache\ContentIrrelevantParametersFilter;
use DrdPlus\RulesSkeleton\Cache\ContentIrrelevantRequestAliases;
use DrdPlus\RulesSkeleton\Cache\RequestCachingPermissionProvider;
use DrdPlus\RulesSkeleton\Cache\RequestHashProvider;
use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\CookiesService;
use DrdPlus\RulesSkeleton\CurrentWebVersion;
use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\RulesApplication;
use DrdPlus\RulesSkeleton\ServicesContainer;
use DrdPlus\RulesSkeleton\UsagePolicy;
use DrdPlus\RulesSkeleton\Web\Main\MainBody;
use Symfony\Component\Process\Process;
use Tests\DrdPlus\RulesSkeleton\Exceptions\GlobalsAreNotBackedUp;
use Tests\DrdPlus\RulesSkeleton\TestsConfiguration;
use Granam\WebVersions\WebVersions;
use Granam\Git\Git;
use Granam\String\StringTools;
use Granam\TestWithMockery\TestWithMockery;
use Granam\Tools\DirName;
use Granam\WebContentBuilder\HtmlDocument;
use Granam\WebContentBuilder\Web\HeadInterface;
use Gt\Dom\Element;
use Mockery\MockInterface;

abstract class AbstractContentTest extends TestWithMockery
{
    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~Tests\\\(.+)Test$~'): string
    {
        return parent::getSutClass($sutTestClass, $regexp);
    }

    use ClassesTrait;

    protected static ?Process $localServerProcess = null;

    private ?Bot $bot = null;
    private ?Git $git = null;
    private ?Dirs $dirs = null;
    private ?Environment $environment = null;
    private ?CookiesService $cookiesService = null;
    private ?Request $request = null;
    protected bool $needPassIn = true;
    protected bool $needPassOut = false;
    private ?Configuration $configuration = null;

    protected function setUp(): void
    {
        if (!\defined('DRD_PLUS_INDEX_FILE_NAME_TO_TEST')) {
            self::markTestSkipped("Missing constant 'DRD_PLUS_INDEX_FILE_NAME_TO_TEST'");
        }
        if ($this->getTestsConfiguration()->hasProtectedAccess()) {
            $this->goIn();
        }
        $this->startLocalWebServer($this->getTestsConfiguration()->getLocalTestingAddress());
    }

    protected function startLocalWebServer(string $localAddress)
    {
        if (!static::$localServerProcess) {
            static::$localServerProcess = new Process(['php', '-S', $localAddress]);
            static::$localServerProcess->start();

            $this->skipTestIfLocalWebServerIsNotRunning();
        }
    }

    protected function getTestsConfiguration(string $class = null): TestsConfiguration
    {
        static $testsConfiguration;
        if ($testsConfiguration === null) {
            /** @var TestsConfiguration|string $class */
            $class ??= TestsConfiguration::class;
            $testsConfiguration = $class::createFromYaml(DRD_PLUS_TESTS_ROOT . '/tests_configuration.yml');
        }

        return $testsConfiguration;
    }

    protected function goIn(): bool
    {
        $_COOKIE[UsagePolicy::OWNERSHIP_COOKIE_NAME] = $this->getNameForLocalOwnershipConfirmation();
        $_COOKIE[$this->getNameForLocalOwnershipConfirmation()] = true; // this cookie simulates confirmation of ownership
        $usagePolicy = new UsagePolicy(
            $this->getVariablePartOfNameForGateway(),
            $request = Request::createFromGlobals($this->getBot(), $this->getEnvironment()),
            $this->createCookiesService($request)
        );
        self::assertTrue(
            $usagePolicy->hasVisitorConfirmedOwnership(),
            "Ownership has not been confirmed by cookie '{$this->getNameForLocalOwnershipConfirmation()}'"
        );
        $this->needPassOut = false;
        $this->needPassIn = true;

        return true;
    }

    protected function goOut(): bool
    {
        unset($_COOKIE[$this->getNameForLocalOwnershipConfirmation()], $_COOKIE[UsagePolicy::OWNERSHIP_COOKIE_NAME]);
        $usagePolicy = new UsagePolicy(
            $this->getVariablePartOfNameForGateway(),
            $request = Request::createFromGlobals($this->getBot(), $this->getEnvironment()),
            $this->createCookiesService($request)
        );
        self::assertFalse(
            $usagePolicy->hasVisitorConfirmedOwnership(),
            "Ownership is still confirmed by cookie '{$this->getNameForLocalOwnershipConfirmation()}'"
        );
        $this->needPassOut = true;
        $this->needPassIn = false;

        return true;
    }

    /**
     * @param array $get = []
     * @param array $post = []
     * @param array $cookies = []
     * @param string $url = '/'
     * @return string
     */
    protected function getContent(array $get = [], array $post = [], array $cookies = [], string $url = '/'): string
    {
        static $contents = [];
        $key = $this->createKey($get, $post, $cookies, $url);
        if (($contents[$key] ?? null) === null) {
            $originalGet = $_GET;
            $originalPost = $_POST;
            $originalCookies = $_COOKIE;
            $originalRequestUri = $_SERVER['REQUEST_URI'] ?? null;
            if ($get) {
                $_GET = array_merge($_GET, $get);
            }
            if ($post) {
                $_POST = array_merge($_POST, $post);
            }
            if ($cookies) {
                $_COOKIE = array_merge($_COOKIE, $cookies);
            }
            $_SERVER['REQUEST_URI'] = $url;
            if ($_GET) {
                $_SERVER['REQUEST_URI'] .= '?' . http_build_query($_GET);
            }
            if ($this->needPassIn()) {
                $this->goIn();
            } elseif ($this->needPassOut()) {
                $this->goOut();
            }
            ob_start();
            include DRD_PLUS_INDEX_FILE_NAME_TO_TEST;
            $contents[$key] = ob_get_clean();
            $_POST = $originalPost;
            $_GET = $originalGet;
            $_COOKIE = $originalCookies;
            $_SERVER['REQUEST_URI'] = $originalRequestUri;
            self::assertNotEmpty(
                $contents[$key],
                'Nothing has been fetched with GET ' . var_export($get, true) . ', POST ' . var_export($post, true)
                . ' and COOKIE ' . var_export($cookies, true)
                . ' from ' . DirName::getPathWithResolvedParents(DRD_PLUS_INDEX_FILE_NAME_TO_TEST)
            );
        }

        return $contents[$key];
    }

    protected function createKey(array $get, array $post, array $cookies, string $url): string
    {
        return json_encode($get, JSON_THROW_ON_ERROR) . '-' . json_encode($post, JSON_THROW_ON_ERROR) . '-' . json_encode($cookies, JSON_THROW_ON_ERROR) . '-' . $url . (int)$this->needPassIn() . (int)$this->needPassOut();
    }

    protected function needPassIn(): bool
    {
        return $this->needPassIn;
    }

    protected function needPassOut(): bool
    {
        return $this->needPassOut;
    }

    /**
     * @param array $get
     * @param array $post
     * @param array $cookies
     * @param string $url = '/'
     * @return HtmlDocument
     */
    protected function getHtmlDocument(array $get = [], array $post = [], array $cookies = [], string $url = '/'): HtmlDocument
    {
        static $htmlDocuments = [];
        $key = $this->createKey($get, $post, $cookies, $url);
        if (($htmlDocuments[$key] ?? null) === null) {
            $htmlDocuments[$key] = new HtmlDocument($this->getContent($get, $post, $cookies, $url));
        }

        return $htmlDocuments[$key];
    }

    protected function fetchHtmlDocumentFromLocalUrl(): HtmlDocument
    {
        $content = $this->fetchContentFromUrl($this->getTestsConfiguration()->getLocalUrl(), true)['content'];
        self::assertNotEmpty($content);

        return new HtmlDocument($content);
    }

    protected function getCurrentPageTitle(HTMLDocument $document = null): string
    {
        $head = ($document ?? $this->getHtmlDocument())->head;
        if (!$head) {
            return '';
        }
        $titles = $head->getElementsByTagName('title');
        if ($titles->count() === 0) {
            return '';
        }
        $titles->rewind();

        return $titles->current()->nodeValue;
    }

    protected function getHtmlHelper(): HtmlHelper
    {
        static $htmlHelper;
        if ($htmlHelper === null) {
            $htmlHelper = $this->createHtmlHelper();
        }

        return $htmlHelper;
    }

    /**
     * @param Dirs|null $dirs
     * @param string|null $forcedMode
     * @param bool $shouldHideCovered
     * @return HtmlHelper|\Mockery\MockInterface
     */
    protected function createHtmlHelper(
        Dirs $dirs = null,
        string $forcedMode = null,
        bool $shouldHideCovered = false
    ): HtmlHelper
    {
        $dirs ??= $this->getDirs();
        $env = $this->createEnvironment($forcedMode);

        return new HtmlHelper(
            $dirs,
            $env->isOnForcedDevelopmentMode(),
            $shouldHideCovered
        );
    }

    protected function createEnvironment(
        string $forcedMode = null,
        string $phpSapi = PHP_SAPI,
        string $projectEnvironment = null,
        string $remoteAddress = null
    ): Environment
    {
        $environmentClass = $this->getEnvironmentClass();
        return new $environmentClass(
            $phpSapi,
            $projectEnvironment ?? $_ENV['PROJECT_ENVIRONMENT'] ?? null,
            $remoteAddress ?? $_SERVER['REMOTE_ADDR'] ?? null,
            $forcedMode ?? $_GET['mode'] ?? null
        );
    }

    protected function fetchNonCachedContent(RulesApplication $rulesApplication = null, bool $backupGlobals = true): string
    {
        $originalGet = $_GET;
        $originalPost = $_POST;
        $originalCookies = $_COOKIE;
        $originalRequest = $_REQUEST;
        $rulesApplication ??= null;
        $_GET[Request::CACHE] = Request::DISABLE;
        ob_start();
        include DRD_PLUS_INDEX_FILE_NAME_TO_TEST;
        $content = ob_get_clean();
        if ($backupGlobals) {
            $_GET = $originalGet;
            $_POST = $originalPost;
            $_COOKIE = $originalCookies;
            $_REQUEST = $originalRequest;
        } else {
            $this->guardGlobalsHaveBackup();
        }

        return $content;
    }

    private function guardGlobalsHaveBackup()
    {
        if (!$this->backupGlobals) {
            throw new GlobalsAreNotBackedUp(<<<TEXT
Global properties should be backed up via annotation
/**
 * @backupGlobals enabled
 */
or via backupGlobals="true" directive in phpunit.xml.dist file
TEXT
            );
        }
    }

    protected const WITH_BODY = true;
    protected const WITHOUT_BODY = true;

    protected function fetchContentFromUrl(string $url, bool $withBody, array $post = [], array $cookies = [], array $headers = []): array
    {
        $this->skipTestIfLocalWebServerIsNeededButNotRunning($url);

        static $cachedContent = [];
        $key = ($withBody ? '1' : '0') . $this->createKey($headers, $post, $cookies, $url);
        if (($cachedContent[$key] ?? null) === null) {

            $curl = curl_init($url);
            curl_setopt($curl, \CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, \CURLOPT_CONNECTTIMEOUT, 7);
            if (!$withBody) {
                // to get headers only
                curl_setopt($curl, \CURLOPT_HEADER, 1);
                curl_setopt($curl, \CURLOPT_NOBODY, 1);
            }
            curl_setopt(
                $curl,
                \CURLOPT_USERAGENT,
                'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:95.0) Gecko/20100101 Firefox/95.0'
            );
            if ($post) {
                curl_setopt($curl, \CURLOPT_POSTFIELDS, $post);
            }
            if ($cookies) {
                $cookieData = [];
                foreach ($cookies as $name => $value) {
                    $cookieData[] = "$name=$value";
                }
                curl_setopt($curl, \CURLOPT_COOKIE, implode('; ', $cookieData));
            }
            foreach ($headers as $headerName => $headerValue) {
                curl_setopt($curl, \CURLOPT_HEADER, "$headerName=$headerValue");
            }
            $content = curl_exec($curl);
            $responseHttpCode = curl_getinfo($curl, \CURLINFO_HTTP_CODE);
            $redirectUrl = curl_getinfo($curl, \CURLINFO_REDIRECT_URL);
            $curlError = curl_error($curl);
            curl_close($curl);
            $cachedContent[$key] = [
                'responseHttpCode' => $responseHttpCode,
                'redirectUrl' => $redirectUrl,
                'content' => $content,
                'error' => $curlError,
            ];
        }

        return $cachedContent[$key];
    }

    protected function skipTestIfLocalWebServerIsNeededButNotRunning(string $url)
    {
        if (!str_starts_with($url, $this->getTestsConfiguration()->getLocalUrl())) {
            return;
        }
        $this->skipTestIfLocalWebServerIsNotRunning();
    }

    protected function skipTestIfLocalWebServerIsNotRunning()
    {
        if (!static::$localServerProcess->isRunning()) {
            self::markTestSkipped(
                sprintf(
                    "Local web server via `%s` controlled by tests is not running. Exit code %d (%s), message: '%s'",
                    (string)static::$localServerProcess->getCommandLine(),
                    static::$localServerProcess->getExitCode(),
                    static::$localServerProcess->getExitCodeText(),
                    trim(static::$localServerProcess->getErrorOutput())
                )
            );
        }
    }

    protected function runCommand(string $command): array
    {
        exec("$command 2>&1", $output, $returnCode);
        self::assertSame(0, $returnCode, "Failed command '$command', got output " . var_export($output, true));

        return $output;
    }

    protected function executeCommand(string $command): string
    {
        $output = $this->runCommand($command);

        return \end($output) ?: '';
    }

    /**
     * @param HtmlDocument $document
     * @return array|Element[]
     */
    protected function getMetaRefreshes(HtmlDocument $document): array
    {
        $metaElements = $document->head->getElementsByTagName('meta');
        $metaRefreshes = [];
        foreach ($metaElements as $metaElement) {
            if ($metaElement->getAttribute('http-equiv') === 'Refresh') {
                $metaRefreshes[] = $metaElement;
            }
        }

        return $metaRefreshes;
    }

    protected function getGitFolderIgnoring(string $dirToCheck): array
    {
        $documentRootEscaped = \escapeshellarg($this->getProjectRoot());
        $dirToCheckEscaped = \escapeshellarg($dirToCheck);
        $command = "git -C $documentRootEscaped check-ignore $dirToCheckEscaped 2>&1";
        \exec($command, $output, $resultCode);
        if ($resultCode > 1) { // both 0 and 1 are valid success return codes in this case
            throw new \RuntimeException(
                "Can not find out if is vendor dir versioned or not by command '{$command}'"
                . ", got return code '{$resultCode}' and output\n"
                . implode("\n", $output)
            );
        }

        return ['output' => $output, 'resultCode' => $resultCode];
    }

    protected function getConfiguration(Dirs $dirs = null): Configuration
    {
        if ($this->configuration === null) {
            $configurationClass = $this->getConfigurationClass();
            $this->configuration = $configurationClass::createFromYml($dirs ?? $this->getDirs());
        }

        return $this->configuration;
    }

    protected function getContentIrrelevantRequestAliases(): ContentIrrelevantRequestAliases
    {
        static $contentIrrelevantRequestAliases;
        if ($contentIrrelevantRequestAliases === null) {
            $contentIrrelevantRequestAliases = $this->createServicesContainer()->getContentIrrelevantRequestAliases();
        }
        return $contentIrrelevantRequestAliases;
    }

    protected function getRequestCachingPermissionProvider(): RequestCachingPermissionProvider
    {
        static $requestCachingPermissionProvider;
        if ($requestCachingPermissionProvider === null) {
            $requestCachingPermissionProvider = $this->createServicesContainer()->getRequestCachingPermissionProvider();
        }
        return $requestCachingPermissionProvider;
    }

    protected function createRequestCachingPermissionProvider(Request $request = null): RequestCachingPermissionProvider
    {
        return new RequestCachingPermissionProvider($request ?? $this->getRequest());
    }

    protected function getRequestHashProvider(): RequestHashProvider
    {
        static $requestHashProvider;
        if ($requestHashProvider === null) {
            $requestHashProvider = $this->createServicesContainer()->getRequestHashProvider();
        }
        return $requestHashProvider;
    }

    protected function createRequestHashProvider(
        Request $request = null,
        ContentIrrelevantRequestAliases $contentIrrelevantRequestAliases = null,
        ContentIrrelevantParametersFilter $contentIrrelevantParametersFilter = null
    ): RequestHashProvider
    {
        return new RequestHashProvider(
            $request ?? $this->getRequest(),
            $contentIrrelevantRequestAliases ?? $this->getContentIrrelevantRequestAliases(),
            $contentIrrelevantParametersFilter ?? $this->getContentIrrelevantParametersFilter()
        );
    }

    protected function getContentIrrelevantParametersFilter(): ContentIrrelevantParametersFilter
    {
        static $contentIrrelevantParametersFilter;
        if ($contentIrrelevantParametersFilter === null) {
            $contentIrrelevantParametersFilter = $this->createServicesContainer()->getContentIrrelevantParametersFilter();
        }
        return $contentIrrelevantParametersFilter;
    }

    /**
     * @param array $customSettings
     * @param Dirs|null $dirs = null
     * @return Configuration|MockInterface
     */
    protected function createCustomConfiguration(array $customSettings, Dirs $dirs = null): Configuration
    {
        $originalConfiguration = $this->getConfiguration();
        $configurationClass = \get_class($originalConfiguration);
        return new $configurationClass(
            $dirs ?? $originalConfiguration->getDirs(),
            array_replace_recursive($originalConfiguration->getValues(), $customSettings)
        );
    }

    protected function createRulesApplication(ServicesContainer $servicesContainer = null)
    {
        $rulesApplicationClass = $this->getRulesApplicationClass();

        return new $rulesApplicationClass($servicesContainer ?? $this->getServicesContainer());
    }

    protected function getServicesContainer(): ServicesContainer
    {
        static $servicesContainer;
        if ($servicesContainer === null) {
            $servicesContainer = $this->createServicesContainer();
        }
        return $servicesContainer;
    }

    protected function createServicesContainer(
        Configuration $configuration = null,
        Environment $environment = null,
        HtmlHelper $htmlHelper = null
    ): ServicesContainer
    {
        return new ServicesContainer(
            $configuration ?? $this->getConfiguration(),
            $environment ?? $this->getEnvironment(),
            $htmlHelper ?? $this->getHtmlHelper()
        );
    }

    protected function getGit(): Git
    {
        if ($this->git === null) {
            $this->git = $this->createGit();
        }
        return $this->git;
    }

    protected function createGit(): Git
    {
        return new Git();
    }

    protected function getBot(): Bot
    {
        if ($this->bot === null) {
            $botClass = $this->getBotClass();
            $this->bot = new $botClass();
        }
        return $this->bot;
    }

    protected function getEnvironment(): Environment
    {
        if ($this->environment === null) {
            $environmentClass = $this->getEnvironmentClass();
            $this->environment = $environmentClass::createFromGlobals();
        }
        return $this->environment;
    }

    /**
     * @return Dirs|\Granam\WebContentBuilder\Dirs
     */
    protected function getDirs(): \Granam\WebContentBuilder\Dirs
    {
        if ($this->dirs === null) {
            $this->dirs = $this->createDirs($this->getProjectRoot());
        }
        return $this->dirs;
    }

    protected function createDirs(string $projectRoot): Dirs
    {
        $dirsClass = $this->getDirsClass();

        return new $dirsClass($projectRoot);
    }

    protected function getCookiesService(): CookiesService
    {
        if ($this->cookiesService === null) {
            $this->cookiesService = $this->createCookiesService($this->getRequest());
        }
        return $this->cookiesService;
    }

    protected function createCookiesService(Request $request): CookiesService
    {
        $cookiesServiceClass = $this->getCookiesServiceClass();
        return new $cookiesServiceClass($request);
    }

    protected function getRequest(): Request
    {
        if ($this->request === null) {
            $this->request = $this->createRequest();
        }
        return $this->request;
    }

    /**
     * @param array $get
     * @param string $path
     * @return Request
     */
    protected function createRequest(array $get = [], string $path = '/'): Request
    {
        return new Request(
            $this->getBot(),
            $this->getEnvironment(),
            $get,
            [], // post
            [], // cookies
            ['REQUEST_URI' => $path] // server
        );
    }

    /**
     * @return array|\Closure[]
     */
    protected function getLicenceSwitchers(): array
    {
        return [[$this, 'goIn'], [$this, 'goOut']];
    }

    protected function isRulesSkeletonChecked(): bool
    {
        return $this->isSkeletonChecked($this->getRulesSkeletonProjectRoot());
    }

    private function getRulesSkeletonProjectRoot(): string
    {
        return __DIR__ . '/../../../..';
    }

    protected function isSkeletonChecked(string $skeletonDocumentRoot = null): bool
    {
        static $skeletonChecked = [];
        $skeletonDocumentRoot ??= $this->getRulesSkeletonProjectRoot();
        if (($skeletonChecked[$skeletonDocumentRoot] ?? null) === null) {
            $projectRootRealPath = \realpath($this->getProjectRoot());
            self::assertNotEmpty($projectRootRealPath, 'Can not find out real path of project root ' . var_export($this->getProjectRoot(), true));
            $skeletonRootRealPath = \realpath($skeletonDocumentRoot);
            self::assertNotEmpty($skeletonRootRealPath, 'Can not find out real path of skeleton root ' . var_export($skeletonRootRealPath, true));
            $skeletonChecked[$skeletonDocumentRoot] = $projectRootRealPath === $skeletonRootRealPath;
        }

        return $skeletonChecked[$skeletonDocumentRoot];
    }

    protected function getGatewayDocument(bool $notCached = false): HtmlDocument
    {
        if ($notCached) {
            return new HtmlDocument($this->getGatewayContent($notCached));
        }
        static $gatewayDocument;
        if ($gatewayDocument === null) {
            $this->removeOwnerShipConfirmation();
            $gatewayDocument = new HtmlDocument($this->getGatewayContent($notCached));
        }

        return $gatewayDocument;
    }

    protected function getGatewayContent(bool $notCached = false): string
    {
        if ($notCached) {
            $this->removeOwnerShipConfirmation();

            return $this->fetchNonCachedContent();
        }
        static $gatewayContent;
        if ($gatewayContent === null) {
            $this->removeOwnerShipConfirmation();
            $gatewayContent = $this->fetchNonCachedContent();
        }

        return $gatewayContent;
    }

    private function getNameForLocalOwnershipConfirmation(): string
    {
        static $cookieName;
        if ($cookieName === null) {
            $cookieName = $this->getNameForOwnershipConfirmation();
        }

        return $cookieName;
    }

    protected function getNameForOwnershipConfirmation(): string
    {
        static $nameOfOwnershipConfirmation;
        if ($nameOfOwnershipConfirmation === null) {
            $usagePolicy = new UsagePolicy(
                $this->getVariablePartOfNameForGateway(),
                Request::createFromGlobals($this->getBot(), $this->getEnvironment()),
                $this->getCookiesService()
            );
            try {
                $usagePolicyReflection = new \ReflectionClass(UsagePolicy::class);
            } catch (\ReflectionException $reflectionException) {
                self::fail($reflectionException->getMessage());
                exit;
            }
            /** @noinspection PhpUnhandledExceptionInspection */
            $getName = $usagePolicyReflection->getMethod('getOwnershipName');
            $getName->setAccessible(true);
            $nameOfOwnershipConfirmation = $getName->invoke($usagePolicy);
        }

        return $nameOfOwnershipConfirmation;
    }

    protected function getVariablePartOfNameForGateway(): string
    {
        return StringTools::toVariableName($this->getTestsConfiguration()->getExpectedWebName());
    }

    private function removeOwnerShipConfirmation(): void
    {
        unset($_COOKIE[$this->getNameForLocalOwnershipConfirmation()]);
    }

    /**
     * @param string $show = ''
     * @param string $hide = ''
     * @return string
     */
    protected function getRulesContentForDev(string $show = '', string $hide = ''): string
    {
        static $rulesContentForDev = [];
        if (($rulesContentForDev[$show][$hide] ?? null) === null) {
            $get = ['mode' => 'dev'];
            if ($show !== '') {
                $get['show'] = $show;
            }
            if ($hide !== '') {
                $get['hide'] = $hide;
            }
            $content = $this->getContent($get);
            $rulesContentForDev[$show][$hide] = $content;
            self::assertNotSame($this->getGatewayContent(), $rulesContentForDev[$show]);
        }

        return $rulesContentForDev[$show][$hide];
    }

    protected function getRulesForDevHtmlDocument(string $show = '', string $hide = ''): HTMLDocument
    {
        static $rulesForDevHtmlDocument = [];
        if (($rulesForDevHtmlDocument[$show][$hide] ?? null) === null) {
            $rulesForDevHtmlDocument[$show][$hide] = new HTMLDocument($this->getRulesContentForDev($show, $hide));
        }

        return $rulesForDevHtmlDocument[$show][$hide];
    }

    /**
     * @return string
     */
    protected function getRulesContentForDevWithHiddenCovered(): string
    {
        return $this->getRulesContentForDev('', 'covered');
    }

    /**
     * @return string|Configuration
     */
    protected function getConfigurationClass(): string
    {
        return Configuration::class;
    }

    protected function getRulesApplicationClass(): string
    {
        return RulesApplication::class;
    }

    protected function unifyPath(string $path): string
    {
        $path = \str_replace('\\', '/', $path);
        $path = preg_replace('~/[.](?:/|$)~', '/', $path);

        return $this->squashTwoDots($path);
    }

    private function squashTwoDots(string $path): string
    {
        $originalPath = $path;
        $path = preg_replace('~/[^/.]+/[.]{2}~', '', $path);
        if ($originalPath === $path) {
            return $originalPath; // nothing has been squashed
        }

        return $this->squashTwoDots($path);
    }

    protected function getSkeletonProjectRoot(): string
    {
        if ($this->isSkeletonChecked()) {
            return $this->getProjectRoot();
        }

        return $this->getDirs()->getVendorRoot() . '/drdplus/rules-skeleton';
    }

    protected function getVendorRoot(): string
    {
        return $this->getDirs()->getVendorRoot();
    }

    protected function createWebVersions(Git $git = null, string $repositoryDir = null): WebVersions
    {
        return new WebVersions(
            $git ?? $this->createGit(),
            $repositoryDir ?? $this->getDirs()->getProjectRoot(),
            'master'
        );
    }

    protected function createCurrentWebVersion(
        Dirs $dirs = null,
        Git $git = null,
        WebVersions $webVersions = null
    ): CurrentWebVersion
    {
        /** @var CurrentWebVersion $currentWebVersionClass */
        $currentWebVersionClass = $this->getCurrentWebVersionClass();

        return new $currentWebVersionClass(
            $dirs ?? $this->getDirs(),
            $git ?? $this->createGit(),
            $webVersions ?? $this->createWebVersions($git)
        );
    }

    protected function getCurrentWebVersion(): CurrentWebVersion
    {
        static $currentWebVersion;
        if ($currentWebVersion === null) {
            $currentWebVersion = $this->createCurrentWebVersion();
        }

        return $currentWebVersion;
    }

    protected function getProjectRoot(): string
    {
        static $projectRoot;
        if ($projectRoot === null) {
            self::assertDirectoryExists(\DRD_PLUS_PROJECT_ROOT, 'Project root has not been found');
            $projectRoot = DirName::getPathWithResolvedParents(\DRD_PLUS_PROJECT_ROOT);
        }

        return $projectRoot;
    }

    protected function createEmptyHead(): HeadInterface
    {
        return new class implements HeadInterface
        {
            public function __toString()
            {
                return $this->getValue();
            }

            public function getValue(): string
            {
                return '';
            }

        };
    }

    /**
     * @param string $content
     * @return MainBody|MockInterface
     */
    protected function createMainBody(string $content): MainBody
    {
        return new class($content) extends MainBody
        {
            private string $content;

            public function __construct(string $content)
            {
                $this->content = $content;
            }

            public function getValue(): string
            {
                return $this->content;
            }

            public function preProcessDocument(HtmlDocument $document): HtmlDocument
            {
                return $document;
            }

            public function postProcessDocument(HtmlDocument $document): HtmlDocument
            {
                return $document;
            }
        };
    }

    protected function getComposerConfig(): array
    {
        static $composerConfig;
        if ($composerConfig === null) {
            $composerFilePath = $this->getProjectRoot() . '/composer.json';
            self::assertFileExists($composerFilePath, 'composer.json has not been found in document root');
            $content = \file_get_contents($composerFilePath);
            self::assertNotEmpty($content, "Nothing has been fetched from $composerFilePath, is readable?");
            $composerConfig = json_decode($content, true, 512, JSON_THROW_ON_ERROR /*as array */);
            self::assertIsArray($composerConfig, 'Can not decode composer.json content fetched from ' . $composerFilePath);
        }

        return $composerConfig;
    }

    /**
     * @param HtmlDocument $htmlDocument
     * @return array|string[]
     */
    protected function parseTableIds(HtmlDocument $htmlDocument): array
    {
        $tableIds = [];
        foreach ($htmlDocument->getElementsByTagName('table') as $table) {
            if (\preg_match('~\sid\s*=\s*"(?<id>[^"]+)~', $table->prop_get_outerHTML(), $idMatch)) {
                $tableIds[] = \html_entity_decode($idMatch['id']);
            }
        }

        return $tableIds;
    }

    protected function parseAllIds(HtmlDocument $htmlDocument): array
    {
        if (!\preg_match_all('~\sid\s*=\s*"(?<id>[^"]+)~', $htmlDocument->saveHTML(), $idMatches)) {
            return [];
        }
        $ids = [];
        foreach ($idMatches['id'] as $id) {
            $ids[] = \html_entity_decode($id);
        }
        return $ids;
    }

    /**
     * @test
     */
    public function Globals_are_cleaned(): void
    {
        self::assertCount(0, $_GET, 'Global $_GET is not empty, have you forgot to set @backupGlobals enabled ?, ' . var_export($_GET, true));
        self::assertCount(0, $_POST, 'Global $_POST is not empty, have you forgot to set @backupGlobals enabled ? ' . var_export($_POST, true));
        if (!$this->getTestsConfiguration()->hasProtectedAccess()) {
            self::assertCount(0, $_COOKIE, 'Global $_COOKIE is not empty, have you forgot to set @backupGlobals enabled ? ' . var_export($_COOKIE, true));
        } else {
            $allowedCookieKeys = [
                UsagePolicy::OWNERSHIP_COOKIE_NAME,
                UsagePolicy::TRIAL_COOKIE_NAME,
                UsagePolicy::TRIAL_EXPIRED_AT_COOKIE_NAME,
            ];
            if (isset($_COOKIE[UsagePolicy::OWNERSHIP_COOKIE_NAME])) {
                $allowedCookieKeys[] = $_COOKIE[UsagePolicy::OWNERSHIP_COOKIE_NAME];
            }
            $cookieTrash = array_diff_key($_COOKIE, array_fill_keys($allowedCookieKeys, 'foo'));
            self::assertCount(
                0,
                $cookieTrash,
                'Global $_COOKIE after filtering pass-related values is not empty, have you forgot to set @backupGlobals enabled ? ' . var_export($cookieTrash, true)
            );
        }
    }

    protected function makeExternalDrdPlusLinksLocal(HtmlDocument $htmlDocument): HtmlDocument
    {
        foreach ($this->getHtmlHelper()->getExternalAnchors($htmlDocument) as $externalAnchor) {
            $externalAnchor->setAttribute('href', $this->turnToLocalLink($externalAnchor->getAttribute('href') ?? ''));
        }
        foreach ($this->getHtmlHelper()->getInternalAnchors($htmlDocument) as $internalAnchor) {
            $internalAnchor->setAttribute('href', $this->turnToLocalLink($internalAnchor->getAttribute('href') ?? ''));
        }
        /** @var Element $iFrame */
        foreach ($htmlDocument->getElementsByTagName('iframe') as $iFrame) {
            $iFrame->setAttribute('src', $this->turnToLocalLink($iFrame->getAttribute('src')));
        }

        return $htmlDocument;
    }

    /**
     * Turn link into local version
     * @param string $link
     * @return string
     */
    protected function turnToLocalLink(string $link): string
    {
        return preg_replace(
            $this->getTestsConfiguration()->getPublicToLocalUrlPartRegexp(),
            $this->getTestsConfiguration()->getPublicToLocalUrlReplacement(),
            $link
        );
    }
}
