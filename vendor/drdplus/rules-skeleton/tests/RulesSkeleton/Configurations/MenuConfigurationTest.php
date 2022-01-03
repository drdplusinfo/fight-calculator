<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Configurations;

use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\Configurations\Exceptions\InvalidConfiguration;
use DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration;
use DrdPlus\RulesSkeleton\Configurations\MenuConfiguration;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;

class MenuConfigurationTest extends AbstractContentTest
{

    protected static array $validMenuConfiguration = [
        MenuConfiguration::POSITION_FIXED => true,
        MenuConfiguration::HOME_BUTTON => [
            HomeButtonConfiguration::SHOW_ON_GATEWAY => true,
            HomeButtonConfiguration::SHOW_ON_HOMEPAGE => true,
            HomeButtonConfiguration::SHOW_ON_ROUTES => true,
            HomeButtonConfiguration::TARGET => 'hit!',
        ],
    ];

    /**
     * @test
     */
    public function I_can_ommit_menu_items_at_all()
    {
        $values = static::$validMenuConfiguration;
        self::assertArrayNotHasKey(MenuConfiguration::ITEMS, $values);
        $menuConfiguration = new MenuConfiguration($values, ['foo', 'bar']);
        self::assertSame([], $menuConfiguration->getItems());
    }

    /**
     * @test
     */
    public function I_can_use_empty_menu_items()
    {
        $values = static::$validMenuConfiguration;
        $values[MenuConfiguration::ITEMS] = [];
        $menuConfiguration = new MenuConfiguration($values, ['foo', 'bar']);
        self::assertSame([], $menuConfiguration->getItems());
    }

    /**
     * @test
     */
    public function I_am_stopped_on_non_string_menu_item_keys()
    {
        $values = static::$validMenuConfiguration;
        $values[MenuConfiguration::ITEMS] = '';
        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessageMatches("~'foo[.]bar[.]items'.+''~");
        new MenuConfiguration($values, ['foo', 'bar']);
    }

    /**
     * @test
     */
    public function I_am_stopped_on_missing_home_button_configuration_part()
    {
        $values = static::$validMenuConfiguration;
        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessageMatches("~'foo[.]bar[.]home_button'.+~");
        unset($values[MenuConfiguration::HOME_BUTTON]);
        new MenuConfiguration($values, ['foo', 'bar']);
    }

    /**
     * @test
     */
    public function Missing_home_button_configuration_section_is_created()
    {
        $values = static::$validMenuConfiguration;
        $values[Configuration::SHOW_HOME_BUTTON_ON_HOMEPAGE] = true;
        unset($values[MenuConfiguration::HOME_BUTTON]);

        $menuConfiguration = new MenuConfiguration($values, ['foo', 'bar']);
        self::assertTrue($menuConfiguration->getHomeButtonConfiguration()->isShownOnHomePage());
    }

    /**
     * @test
     */
    public function I_can_set_menu_items()
    {
        $values = static::$validMenuConfiguration;
        $values[MenuConfiguration::ITEMS] = [
            'gate' => 'To tomorrow',
        ];
        $menuConfiguration = new MenuConfiguration($values, ['foo', 'bar']);
        self::assertSame($values[MenuConfiguration::ITEMS], $menuConfiguration->getItems());
    }

    /**
     * @test
     * @dataProvider provideValuesToCheckIfMenuIsShown
     * @param array $items
     * @param bool $homeButtonIsShownOnGateway
     * @param bool $homeButtonIsShownOnHomepage
     * @param bool $homeButtonIsShownOnRoutes
     * @param bool $shouldBeShownOnGateway
     * @param bool $shouldBeShownOnHomepage
     * @param bool $shouldBeShownOnRoutes
     */
    public function I_can_easily_check_if_menu_is_shown_on_homepage_or_routes(
        array $items,
        bool $homeButtonIsShownOnGateway,
        bool $homeButtonIsShownOnHomepage,
        bool $homeButtonIsShownOnRoutes,
        bool $shouldBeShownOnGateway,
        bool $shouldBeShownOnHomepage,
        bool $shouldBeShownOnRoutes
    )
    {
        $values = static::$validMenuConfiguration;
        $values[MenuConfiguration::ITEMS] = $items;
        $values[MenuConfiguration::HOME_BUTTON] = [
            HomeButtonConfiguration::SHOW_ON_GATEWAY => $homeButtonIsShownOnGateway,
            HomeButtonConfiguration::SHOW_ON_HOMEPAGE => $homeButtonIsShownOnHomepage,
            HomeButtonConfiguration::SHOW_ON_ROUTES => $homeButtonIsShownOnRoutes,
        ];
        $menuConfiguration = new MenuConfiguration($values, ['foo', 'bar']);
        self::assertSame($shouldBeShownOnGateway, $menuConfiguration->isShownOnGateway());
        self::assertSame($shouldBeShownOnHomepage, $menuConfiguration->isShownOnHomepage());
        self::assertSame($shouldBeShownOnRoutes, $menuConfiguration->isShownOnRoutes());
    }

    public function provideValuesToCheckIfMenuIsShown(): array
    {
        return [
            'with items and home button everywhere' => [
                ['some key' => 'some item'], // items
                true,                        // homeButtonIsShownOnGateway
                true,                        // homeButtonIsShownOnHomepage
                true,                        // homeButtonIsShownOnRoutes
                true,                        // shouldBeShownOnGateway
                true,                        // shouldBeShownOnHomepage
                true,                        // shouldBeShownOnRoutes
            ],
            'only with items' => [
                ['some key' => 'some item'], // items
                false,                       // homeButtonIsShownOnGateway
                false,                       // homeButtonIsShownOnHomepage
                false,                       // homeButtonIsShownOnRoutes
                true,                        // shouldBeShownOnGateway
                true,                        // shouldBeShownOnHomepage
                true,                        // shouldBeShownOnRoutes
            ],
            'only with home button on gateway' => [
                [],                          // items
                true,                        // homeButtonIsShownOnGateway
                false,                       // homeButtonIsShownOnHomepage
                false,                       // homeButtonIsShownOnRoutes
                true,                        // shouldBeShownOnGateway
                false,                       // shouldBeShownOnHomepage
                false,                       // shouldBeShownOnRoutes
            ],
            'only with home button on homepage' => [
                [],                          // items
                false,                       // homeButtonIsShownOnGateway
                true,                        // homeButtonIsShownOnHomepage
                false,                       // homeButtonIsShownOnRoutes
                false,                       // shouldBeShownOnGateway
                true,                        // shouldBeShownOnHomepage
                false,                       // shouldBeShownOnRoutes
            ],
            'only with home button on routes' => [
                [],                          // items
                false,                       // homeButtonIsShownOnGateway
                false,                       // homeButtonIsShownOnHomepage
                true,                        // homeButtonIsShownOnRoutes
                false,                       // shouldBeShownOnGateway
                false,                       // shouldBeShownOnHomepage
                true,                        // shouldBeShownOnRoutes
            ],
        ];
    }

    /**
     * @test
     */
    public function I_can_create_it_without_defining_if_menu_should_be_fixed(): void
    {
        $values = static::$validMenuConfiguration;
        unset($values[MenuConfiguration::POSITION_FIXED]);
        $menuConfiguration = new MenuConfiguration($values, ['foo']);
        self::assertFalse($menuConfiguration->isPositionFixed());
    }

    /**
     * @test
     */
    public function I_am_stopped_on_non_array_home_button_configuration()
    {
        $values = static::$validMenuConfiguration;
        $values[MenuConfiguration::HOME_BUTTON] = true;

        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessageMatches('~got true~');
        new MenuConfiguration($values, ['foo']);
    }

}
