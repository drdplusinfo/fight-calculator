<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Configurations;

use DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;

class HomeButtonConfigurationTest extends AbstractContentTest
{
    private static array $homeButtonConfigurationValues = [
        HomeButtonConfiguration::SHOW_ON_HOMEPAGE => true,
        HomeButtonConfiguration::SHOW_ON_ROUTES => true,
        HomeButtonConfiguration::TARGET => '/',
        HomeButtonConfiguration::IMAGE => 'foo.jpg',
    ];

    /**
     * @test
     * @dataProvider provideValuesToCheckIfHomeButtonIsShown
     * @param bool $showOnGateway
     * @param bool $showOnHomepage
     * @param bool $showOnRoutes
     */
    public function I_can_easily_check_if_home_button_is_shown_shomewhere(
        bool $showOnGateway,
        bool $showOnHomepage,
        bool $showOnRoutes
    )
    {
        $values = array_merge(
            static::$homeButtonConfigurationValues,
            [
                HomeButtonConfiguration::SHOW_ON_GATEWAY => $showOnGateway,
                HomeButtonConfiguration::SHOW_ON_HOMEPAGE => $showOnHomepage,
                HomeButtonConfiguration::SHOW_ON_ROUTES => $showOnRoutes,
            ]
        );
        $homeButtonConfiguration = new HomeButtonConfiguration($values, ['foo']);
        self::assertSame($showOnGateway, $homeButtonConfiguration->isShownOnGateway());
        self::assertSame($showOnHomepage, $homeButtonConfiguration->isShownOnHomePage());
        self::assertSame($showOnRoutes, $homeButtonConfiguration->isShownOnRoutes());
    }

    /**
     * @test
     */
    public function Home_button_is_shown_by_default()
    {
        $values = static::$homeButtonConfigurationValues;
        unset($values[HomeButtonConfiguration::SHOW_ON_GATEWAY], $values[HomeButtonConfiguration::SHOW_ON_HOMEPAGE], $values[HomeButtonConfiguration::SHOW_ON_ROUTES]);
        $homeButtonConfiguration = new HomeButtonConfiguration($values, ['foo']);
        self::assertTrue($homeButtonConfiguration->isShownOnGateway());
        self::assertTrue($homeButtonConfiguration->isShownOnHomePage());
        self::assertTrue($homeButtonConfiguration->isShownOnRoutes());
    }

    public function provideValuesToCheckIfHomeButtonIsShown(): array
    {
        return [
            'shown everywhere' => [
                HomeButtonConfiguration::SHOW_ON_GATEWAY => true,
                HomeButtonConfiguration::SHOW_ON_HOMEPAGE => true,
                HomeButtonConfiguration::SHOW_ON_ROUTES => true,
                true,
            ],
            'shown only on gateway' => [
                HomeButtonConfiguration::SHOW_ON_GATEWAY => true,
                HomeButtonConfiguration::SHOW_ON_HOMEPAGE => false,
                HomeButtonConfiguration::SHOW_ON_ROUTES => false,
                true,
            ],
            'shown only on homepage' => [
                HomeButtonConfiguration::SHOW_ON_GATEWAY => false,
                HomeButtonConfiguration::SHOW_ON_HOMEPAGE => true,
                HomeButtonConfiguration::SHOW_ON_ROUTES => false,
                true,
            ],
            'shown only on routes' => [
                HomeButtonConfiguration::SHOW_ON_HOMEPAGE => false,
                HomeButtonConfiguration::SHOW_ON_ROUTES => true,
                true,
            ],
            'not shown' => [
                HomeButtonConfiguration::SHOW_ON_GATEWAY => false,
                HomeButtonConfiguration::SHOW_ON_HOMEPAGE => false,
                HomeButtonConfiguration::SHOW_ON_ROUTES => false,
                false,
            ],
        ];
    }
}
