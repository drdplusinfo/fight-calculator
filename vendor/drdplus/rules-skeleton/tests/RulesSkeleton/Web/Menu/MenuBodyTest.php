<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Web\Menu;

use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\Configurations\MenuConfiguration;
use DrdPlus\RulesSkeleton\HomepageDetector;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\RouteMatchingPathProvider;
use DrdPlus\RulesSkeleton\Ticket;
use DrdPlus\RulesSkeleton\Web\Menu\MenuBody;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Granam\WebContentBuilder\HtmlDocument;
use Mockery\MockInterface;

class MenuBodyTest extends AbstractContentTest
{
    /**
     * @test
     * @dataProvider provideConfigurationToShowHomeButton
     * @param bool $showHomeButtonOnHomepage
     * @param bool $showHomeButtonOnRoutes
     * @param bool $onHomepage
     */
    public function I_can_show_home_button(bool $showHomeButtonOnHomepage, bool $showHomeButtonOnRoutes, bool $onHomepage): void
    {
        $configuration = $this->createConfigurationWithCustomMenu([
            MenuConfiguration::SHOW_HOME_BUTTON_ON_HOMEPAGE => $showHomeButtonOnHomepage,
            MenuConfiguration::SHOW_HOME_BUTTON_ON_ROUTES => $showHomeButtonOnRoutes,
        ]);
        $menuOnHomepage = $this->createMenu($configuration->getMenuConfiguration(), $onHomepage, self::CAN_PASS_IN);
        $htmlDocument = new HtmlDocument(<<<HTML
<html lang="cs">
<body>
{$menuOnHomepage->getValue()}
</body>
</html>
HTML
        );
        $homeButton = $htmlDocument->getElementById(HtmlHelper::ID_HOME_BUTTON);
        self::assertNotEmpty($homeButton, 'Home button is missing');
        self::assertSame(
            $this->getTestsConfiguration()->getExpectedHomeButtonTarget(),
            $homeButton->getAttribute('href'), 'Link of home button should lead to home'
        );
    }

    public function provideConfigurationToShowHomeButton(): array
    {
        return [
            'show home button on homepage' => [true, true, true],
            'show home button on homepage only' => [true, false, true],
            'show home button on routes' => [false, true, false],
        ];
    }

    protected function createConfigurationWithCustomMenu(array $menuConfiguration): Configuration
    {
        return $this->createCustomConfiguration([
            Configuration::WEB => [
                Configuration::MENU => $menuConfiguration,
            ],
        ]);
    }

    /**
     * @test
     * @dataProvider provideConfigurationToHideHomeButtonOnHomepage
     * @param bool $showHomeButtonOnHomepage
     * @param bool $showHomeButtonOnRoutes
     */
    public function I_can_hide_home_button_on_homepage(bool $showHomeButtonOnHomepage, bool $showHomeButtonOnRoutes): void
    {
        $configuration = $this->createConfigurationWithCustomMenu([
            MenuConfiguration::SHOW_HOME_BUTTON_ON_HOMEPAGE => $showHomeButtonOnHomepage,
            MenuConfiguration::SHOW_HOME_BUTTON_ON_ROUTES => $showHomeButtonOnRoutes,
        ]);
        $menu = $this->createMenu($configuration->getMenuConfiguration(), self::HOMEPAGE_IS_REQUESTED, self::CAN_PASS_IN);
        $htmlDocument = new HtmlDocument(<<<HTML
<html lang="cs">
<body>
{$menu->getValue()}
</body>
</html>
HTML
        );
        $homeButton = $htmlDocument->getElementById(HtmlHelper::ID_HOME_BUTTON);
        self::assertEmpty($homeButton, 'Home button should not be used at all');
    }

    public function provideConfigurationToHideHomeButtonOnHomepage(): array
    {
        return [
            'hide home button everywhere' => [false, false],
            'show home button only on route' => [false, true],
        ];
    }

    private const HOMEPAGE_IS_REQUESTED = true;
    private const HOMEPAGE_IS_NOT_REQUESTED = false;

    private const CAN_PASS_IN = true;
    private const CAN_NOT_PASS_IN = false;

    private function createMenu(MenuConfiguration $menuConfiguration, bool $isHomepageRequested, bool $canPassIn): MenuBody
    {
        return new MenuBody(
            $menuConfiguration,
            $this->createHomepageDetector($isHomepageRequested),
            $this->createTicket($canPassIn)
        );
    }

    /**
     * @param bool $isHomepageRequested
     * @return HomepageDetector|MockInterface
     */
    private function createHomepageDetector(bool $isHomepageRequested): HomepageDetector
    {
        $homepageDetector = $this->mockery(HomepageDetector::class);
        $homepageDetector->shouldReceive('isHomepageRequested')
            ->andReturn($isHomepageRequested);
        return $homepageDetector;
    }

    /**
     * @param bool $canPassIn
     * @return Ticket|MockInterface
     */
    private function createTicket(bool $canPassIn): Ticket
    {
        $ticket = $this->mockery(Ticket::class);
        $ticket->shouldReceive('canPassIn')
            ->andReturn($canPassIn);
        return $ticket;
    }

    /**
     * @test
     * @dataProvider provideConfigurationToHideHomeButtonOnRoutes
     * @param bool $showHomeButtonOnHomepage
     * @param bool $showHomeButtonOnRoutes
     */
    public function I_can_hide_home_button_on_routes(bool $showHomeButtonOnHomepage, bool $showHomeButtonOnRoutes): void
    {
        $configuration = $this->createConfigurationWithCustomMenu([
            MenuConfiguration::SHOW_HOME_BUTTON_ON_HOMEPAGE => $showHomeButtonOnHomepage,
            MenuConfiguration::SHOW_HOME_BUTTON_ON_ROUTES => $showHomeButtonOnRoutes,
        ]);
        $menu = $this->createMenu($configuration->getMenuConfiguration(), self::HOMEPAGE_IS_NOT_REQUESTED, self::CAN_PASS_IN);
        $htmlDocument = new HtmlDocument(<<<HTML
<html lang="cs">
<body>
{$menu->getValue()}
</body>
</html>
HTML
        );
        $homeButton = $htmlDocument->getElementById(HtmlHelper::ID_HOME_BUTTON);
        self::assertEmpty($homeButton, 'Home button should not be used at all');
    }

    public function provideConfigurationToHideHomeButtonOnRoutes(): array
    {
        return [
            'hide home button everywhere' => [false, false],
            'show home button only on homepage' => [true, false],
        ];
    }

    /**
     * @test
     */
    public function Home_button_target_leads_to_expected_link(): void
    {
        foreach ([true, false] as $showHomeButtonOnHomepage) {
            $configuration = $this->createConfigurationWithCustomMenu([
                MenuConfiguration::SHOW_HOME_BUTTON_ON_HOMEPAGE => $showHomeButtonOnHomepage,
                MenuConfiguration::SHOW_HOME_BUTTON_ON_ROUTES => !$showHomeButtonOnHomepage,
                MenuConfiguration::HOME_BUTTON_TARGET => $expectedTarget = '/foo/bar?baz=qux',
            ]);
            $menu = $this->createMenu($configuration->getMenuConfiguration(), $showHomeButtonOnHomepage, self::CAN_PASS_IN);
            $htmlDocument = new HtmlDocument(<<<HTML
<html lang="cs">
<body>
{$menu->getValue()}
</body>
</html>
HTML
            );
            $homeButton = $htmlDocument->getElementById(HtmlHelper::ID_HOME_BUTTON);
            self::assertNotEmpty($homeButton, 'Home button expected');
            self::assertSame($expectedTarget, $homeButton->getAttribute('href'));
        }
    }

    /**
     * @test
     */
    public function I_can_get_menu_even_on_not_found_route(): void
    {
        $configuration = $this->createConfigurationWithCustomMenu([
            MenuConfiguration::SHOW_HOME_BUTTON_ON_HOMEPAGE => false,
            MenuConfiguration::SHOW_HOME_BUTTON_ON_ROUTES => true,
            MenuConfiguration::HOME_BUTTON_TARGET => $expectedTarget = '/foo/bar?baz=qux',
        ]);
        $menu = new MenuBody(
            $configuration->getMenuConfiguration(),
            new HomepageDetector(
                new RouteMatchingPathProvider(
                    $this->getServicesContainer()->getRulesUrlMatcher(),
                    uniqid('/non/existing/path', true)
                )
            ),
            $this->createTicket(self::CAN_PASS_IN)
        );
        $htmlDocument = new HtmlDocument(<<<HTML
<html lang="cs">
<body>
{$menu->getValue()}
</body>
</html>
HTML
        );
        $homeButton = $htmlDocument->getElementById(HtmlHelper::ID_HOME_BUTTON);
        self::assertNotEmpty($homeButton, 'Home button is missing');
        self::assertSame(
            $expectedTarget,
            $homeButton->getAttribute('href'), 'Link of home button should lead to home'
        );
    }
}
